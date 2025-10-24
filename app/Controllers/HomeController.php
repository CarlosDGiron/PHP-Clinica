<?php
class HomeController extends Controllers {
    public function __construct() {
        $this->authMiddleware();
    }
    
    public function index() {
        $this->view("HomeView");
    }

    public function token(){
        $data = $this->authMiddleware->validateToken();
        $response = $this->getUserAndModules($data);
        $this->jsonResponse($response);
    }

    // Devuelve la noticia más relevante de cada API externa con UA, RSS y caché
    public function news() {
        // -------- config --------
        $cacheDir  = APP_ROUTE . "/storage/cache";
        $cacheFile = $cacheDir . "/news.json";
        $ttl       = 300; // 5 min

        if (!is_dir($cacheDir)) {
            @mkdir($cacheDir, 0775, true);
        }

        $now = time();
        $errors = [];
        $items  = [];

        // Helper: fetch universal con UA + headers
        $ua = "ClinicaMasterBot/1.0 (+https://example.local) PHP/" . PHP_VERSION;
        $defaultHeaders = [
            "User-Agent: " . $ua,
            "Accept: application/json, application/xml;q=0.9, text/xml;q=0.8, */*;q=0.5",
            "Accept-Language: es-ES,es;q=0.9,en;q=0.8",
            "Connection: close"
        ];

        $fetchCurl = function(string $url, int $timeout = 10, array $headers = []) use ($defaultHeaders) {
            $ch = curl_init($url);
            $hdrs = array_merge($defaultHeaders, $headers);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_MAXREDIRS      => 3,
                CURLOPT_CONNECTTIMEOUT => $timeout,
                CURLOPT_TIMEOUT        => $timeout,
                CURLOPT_HTTPHEADER     => $hdrs,
                CURLOPT_USERAGENT      => preg_replace('/^User-Agent:\\s*/i','',$hdrs[0] ?? 'ClinicaMasterBot'),
                CURLOPT_ENCODING       => '',
                CURLOPT_SSL_VERIFYPEER => true,
                CURLOPT_SSL_VERIFYHOST => 2,
            ]);
            $body = curl_exec($ch);
            $ct   = curl_getinfo($ch, CURLINFO_CONTENT_TYPE) ?: '';
            $code = curl_getinfo($ch, CURLINFO_HTTP_CODE) ?: 0;
            $err  = curl_error($ch);
            curl_close($ch);
            return [$code, $body, $ct, $err];
        };

        // Normalizador de ítems
        $norm = function($title,$source,$url,$image=null,$published=null){
            return [
                "title"     => is_string($title)? trim($title) : "",
                "source"    => is_string($source)? trim($source) : "",
                "url"       => is_string($url)? trim($url) : "",
                "image"     => $image ?: "",
                "published" => $published ? (is_numeric($published)? (int)$published : (strtotime($published) ?: null)) : null
            ];
        };

        // Resolver URL relativa -> absoluta
        $resolveUrl = function(string $base, string $rel) {
            if ($rel === '' ) return '';
            if (preg_match('~^https?://~i', $rel)) return $rel;
            // evitar data/blob
            if (preg_match('~^(data:|blob:)~i', $rel)) return '';
            $bp = @parse_url($base);
            if (!$bp || empty($bp['scheme']) || empty($bp['host'])) return $rel;
            $scheme = $bp['scheme'];
            $host   = $bp['host'];
            $port   = isset($bp['port']) ? (":".$bp['port']) : '';
            $path   = isset($bp['path']) ? $bp['path'] : '/';
            if (strpos($rel, '//') === 0) return $scheme . ':' . $rel;
            if ($rel[0] === '/') return $scheme . '://' . $host . $port . $rel;
            $dir = rtrim(substr($path, 0, strrpos($path, '/') !== false ? strrpos($path, '/')+1 : 0), '/');
            return $scheme . '://' . $host . $port . '/' . ltrim($dir . '/' . $rel, '/');
        };

        // ¿Es URL de video conocida por extensión?
        $isVideoUrl = function(string $u){
            return (bool)preg_match('~\.(mp4|webm|ogg|ogv|mov|m4v|mpg|mpeg|m3u8)(\?|#|$)~i', $u);
        };

        // Extraer imagen/video de una página HTML
        $extractMediaFromHtml = function(string $html, string $baseUrl) use ($resolveUrl, $isVideoUrl) {
            $candidates = [];
            // 1) Meta tags (OG/Twitter)
            if (preg_match_all('/<meta[^>]+(?:property|name)=["\']([^"\']+)["\'][^>]*content=["\']([^"\']+)["\'][^>]*>/i', $html, $m, PREG_SET_ORDER)) {
                foreach ($m as $mm) {
                    $name = strtolower(trim($mm[1]));
                    $val  = trim($mm[2]);
                    if ($val==='') continue;
                    if ($name==='og:image' || $name==='og:image:url' || $name==='og:image:secure_url' || $name==='twitter:image' || $name==='twitter:image:src') {
                        $candidates[] = $val;
                    }
                    if ($name==='og:video' || $name==='og:video:url' || $name==='og:video:secure_url') {
                        $candidates[] = $val; // puede ser video
                    }
                    if ($name==='image' || $name==='thumbnail') {
                        $candidates[] = $val;
                    }
                }
            }
            // 2) JSON-LD
            if (preg_match_all('/<script[^>]+type=\"application\/ld\+json\"[^>]*>([\s\S]*?)<\/script>/i', $html, $ms)) {
                foreach ($ms[1] as $blk) {
                    $blk = trim($blk);
                    if ($blk==='') continue;
                    $json = json_decode($blk, true);
                    if (json_last_error() !== JSON_ERROR_NONE) continue;
                    $stack = [$json];
                    while ($stack) {
                        $node = array_pop($stack);
                        if (is_array($node)) {
                            if (isset($node['image'])) {
                                $img = $node['image'];
                                if (is_string($img)) $candidates[] = $img;
                                elseif (is_array($img)) {
                                    if (isset($img['url']) && is_string($img['url'])) $candidates[] = $img['url'];
                                    else foreach ($img as $vv) if (is_string($vv)) $candidates[] = $vv;
                                }
                            }
                            if (isset($node['thumbnailUrl']) && is_string($node['thumbnailUrl'])) $candidates[] = $node['thumbnailUrl'];
                            if (isset($node['contentUrl']) && is_string($node['contentUrl'])) $candidates[] = $node['contentUrl'];
                            foreach ($node as $k=>$v) if (is_array($v)) $stack[] = $v;
                        }
                    }
                }
            }
            // 3) <link rel="image_src">
            if (preg_match_all('/<link[^>]+rel=["\']image_src["\'][^>]*href=["\']([^"\']+)["\'][^>]*>/i', $html, $lm)) {
                foreach ($lm[1] as $u) $candidates[] = trim($u);
            }
            // 4) <picture>/<source srcset> y <img>
            // srcset: tomar la primera URL
            if (preg_match_all('/<source[^>]+srcset=["\']([^"\']+)["\'][^>]*>/i', $html, $sm)) {
                foreach ($sm[1] as $srcset) {
                    $parts = preg_split('/\s*,\s*/', trim($srcset));
                    if (!empty($parts)) {
                        $first = trim(preg_split('/\s+/', $parts[0])[0]);
                        if ($first) $candidates[] = $first;
                    }
                }
            }
            if (preg_match_all('/<img[^>]+(?:src|data-src|data-original|data-lazy-src)=["\']([^"\']+)["\'][^>]*>/i', $html, $im)) {
                foreach ($im[1] as $u) $candidates[] = trim($u);
            }

            // Normalizar y elegir la primera válida
            foreach ($candidates as $u) {
                $u = trim($u);
                if ($u==='') continue;
                if (preg_match('~^(data:|blob:)~i', $u)) continue;
                $abs = $resolveUrl($baseUrl, $u);
                if ($abs==='') continue;
                // Preferir video si es claramente video (el front reproduce <video>)
                if ($isVideoUrl($abs)) return $abs;
                // Imágenes comunes
                if (preg_match('~\.(png|jpe?g|gif|webp|avif|svg)(\?|#|$)~i', $abs)) return $abs;
            }
            // Si ninguna tiene extensión, devolver la primera candidata absolutizada
            foreach ($candidates as $u) {
                $abs = $resolveUrl($baseUrl, $u);
                if ($abs) return $abs;
            }
            return '';
        };

        // Cargar keys (si existen)
        @require_once APP_ROUTE . "/config/NewsApiKeys.php";

        // 1) NewsAPI
        if (defined('NEWSAPI_KEY') && NEWSAPI_KEY && NEWSAPI_KEY !== 'TU_KEY_NEWSAPI') {
            $q = urlencode('salud OR medicina OR hospital OR vacuna');
            $url = "https://newsapi.org/v2/everything?q={$q}&language=es&sortBy=publishedAt&pageSize=10";
            list($code,$body,$ct,$err) = $fetchCurl($url, 10, ["X-Api-Key: ".NEWSAPI_KEY]);
            if ($code===200 && $body) {
                $json = json_decode($body, true);
                if (isset($json['articles'][0])) {
                    $a = $json['articles'][0];
                    $items['newsapi'] = $norm(
                        $a['title'] ?? '',
                        $a['source']['name'] ?? 'NewsAPI',
                        $a['url'] ?? '',
                        $a['urlToImage'] ?? '',
                        $a['publishedAt'] ?? null
                    );
                } else { $errors[]="NewsAPI: payload vacío"; }
            } else { $errors[]="NewsAPI: HTTP {$code} {$err}"; }
        } else {
            $errors[]="NewsAPI: KEY no configurada";
        }

        // 2) Mediastack
        if (defined('MEDIASTACK_KEY') && MEDIASTACK_KEY && MEDIASTACK_KEY!=='TU_KEY_MEDIASTACK') {
            $params = http_build_query([
                'access_key' => MEDIASTACK_KEY,
                'languages'  => 'es',
                'categories' => 'health',
                'sort'       => 'published_desc',
                'limit'      => 10,
            ]);
            $url = "http://api.mediastack.com/v1/news?{$params}";
            list($code,$body,$ct,$err) = $fetchCurl($url);
            if ($code===200 && $body) {
                $json = json_decode($body, true);
                if (isset($json['data'][0])) {
                    $a = $json['data'][0];
                    $items['mediastack'] = $norm(
                        $a['title'] ?? '',
                        $a['source'] ?? 'mediastack',
                        $a['url'] ?? '',
                        $a['image'] ?? '',
                        $a['published_at'] ?? null
                    );
                } else { $errors[]="Mediastack: payload vacío"; }
            } else { $errors[]="Mediastack: HTTP {$code} {$err}"; }
        } else {
            $errors[]="Mediastack: KEY no configurada";
        }

        // 3) GNews
        if (defined('GNEWS_KEY') && GNEWS_KEY && GNEWS_KEY!=='TU_KEY_GNEWS') {
            $q = urlencode('salud OR medicina OR hospital OR vacuna Guatemala');
            $url = "https://gnews.io/api/v4/search?q={$q}&lang=es&max=10&apikey=" . urlencode(GNEWS_KEY);
            list($code,$body,$ct,$err) = $fetchCurl($url);
            if ($code===200 && $body) {
                $json = json_decode($body, true);
                if (isset($json['articles'][0])) {
                    $a = $json['articles'][0];
                    $items['gnews'] = $norm(
                        $a['title'] ?? '',
                        $a['source']['name'] ?? 'GNews',
                        $a['url'] ?? '',
                        $a['image'] ?? '',
                        $a['publishedAt'] ?? null
                    );
                } else { $errors[]="GNews: payload vacío"; }
            } else { $errors[]="GNews: HTTP {$code} {$err}"; }
        } else {
            $errors[]="GNews: KEY no configurada";
        }

        // 4) NewsData
        if (defined('NEWSDATA_KEY') && NEWSDATA_KEY && NEWSDATA_KEY!=='TU_KEY_NEWSDATA') {
            $url = "https://newsdata.io/api/1/news?apikey=" . urlencode(NEWSDATA_KEY) . "&country=gt&category=health&language=es";
            list($code,$body,$ct,$err) = $fetchCurl($url);
            if ($code===200 && $body) {
                $json = json_decode($body, true);
                if (isset($json['results'][0])) {
                    $a = $json['results'][0];
                    $items['newsdata'] = $norm(
                        $a['title'] ?? '',
                        $a['source_id'] ?? 'NewsData',
                        $a['link'] ?? '',
                        $a['image_url'] ?? '',
                        $a['pubDate'] ?? null
                    );
                } else { $errors[]="NewsData: payload vacío"; }
            } else { $errors[]="NewsData: HTTP {$code} {$err}"; }
        } else {
            $errors[]="NewsData: KEY no configurada";
        }

        // RSS de respaldo
        if (count($items) < 4) {
            $parseRss = function($xmlString) {
                if (!is_string($xmlString) || $xmlString==='') return [];
                $out = [];
                $simpleOk = function_exists('simplexml_load_string') && function_exists('libxml_use_internal_errors');
                if ($simpleOk) {
                    libxml_use_internal_errors(true);
                    $sx = @simplexml_load_string($xmlString);
                    if ($sx && isset($sx->channel->item)) {
                        foreach ($sx->channel->item as $it) {
                            $title = (string)($it->title ?? '');
                            $link  = (string)($it->link ?? '');
                            $pub   = (string)($it->pubDate ?? '');
                            $img   = '';
                            if (isset($it->enclosure) && $it->enclosure['url']) {
                                $img = (string)$it->enclosure['url'];
                            } elseif (isset($it->children('media', true)->content)) {
                                $mc = $it->children('media', true)->content;
                                if ($mc && $mc->attributes()['url']) $img = (string)$mc->attributes()['url'];
                            } elseif (isset($it->children('media', true)->thumbnail)) {
                                $mt = $it->children('media', true)->thumbnail;
                                if ($mt && $mt->attributes()['url']) $img = (string)$mt->attributes()['url'];
                            } elseif (isset($it->description)) {
                                $desc = (string)$it->description;
                                if ($desc) {
                                    if (preg_match('/<img[^>]+src=["\']([^"\']+)["\']/i', $desc, $m)) {
                                        $img = $m[1];
                                    }
                                }
                            }
                            $out[] = ["title"=>$title,"url"=>$link,"image"=>$img,"published"=>$pub];
                        }
                    }
                    libxml_clear_errors();
                }
                if (empty($out)) {
                    $dom = new \DOMDocument();
                    @$dom->loadXML($xmlString);
                    $nodes = $dom->getElementsByTagName('item');
                    foreach ($nodes as $node) {
                        $title = $node->getElementsByTagName('title')->item(0);
                        $link  = $node->getElementsByTagName('link')->item(0);
                        $pub   = $node->getElementsByTagName('pubDate')->item(0);
                        $img   = '';
                        $enclosures = $node->getElementsByTagName('enclosure');
                        if ($enclosures->length>0) {
                            $img = $enclosures->item(0)->getAttribute('url');
                        } else {
                            $thumbs = $node->getElementsByTagName('thumbnail');
                            if ($thumbs->length>0) {
                                $img = $thumbs->item(0)->getAttribute('url');
                            } else {
                                $descNode = $node->getElementsByTagName('description')->item(0);
                                if ($descNode) {
                                    $desc = $descNode->nodeValue;
                                    if ($desc && preg_match('/<img[^>]+src=["\']([^"\']+)["\']/i', $desc, $m)) {
                                        $img = $m[1];
                                    }
                                }
                            }
                        }
                        $out[] = [
                            "title" => $title? $title->nodeValue : '',
                            "url"   => $link?  $link->nodeValue : '',
                            "image" => $img,
                            "published" => $pub? $pub->nodeValue : ''
                        ];
                    }
                }
                return $out;
            };

            $rssList = [
                ["key"=>"rss1", "url"=>"https://www.paho.org/es/noticias/feed", "source"=>"PAHO/OPS"],
                ["key"=>"rss2", "url"=>"https://www.who.int/rss-feeds/news-english.xml", "source"=>"WHO"],
                ["key"=>"rss3", "url"=>"https://cnnespanol.cnn.com/category/salud/feed/", "source"=>"CNN Español"],
                ["key"=>"rss4", "url"=>"https://feeds.elpais.com/mrss-s/pages/ep/site/elpais.com/section/salud-y-bienestar/portada", "source"=>"El País Salud"],
                ["key"=>"rss5", "url"=>"https://medlineplus.gov/spanish/rss/news_es.xml", "source"=>"MedlinePlus"],
                ["key"=>"rss6", "url"=>"https://www.nih.gov/news-events/news-releases/feed", "source"=>"NIH"],
                ["key"=>"rss7", "url"=>"https://techcrunch.com/tag/healthtech/feed/", "source"=>"TechCrunch Health"],
            ];

            foreach ($rssList as $rss) {
                if (isset($items[$rss['key']])) continue;
                list($code,$body,$ct,$err) = $fetchCurl($rss['url'], 10);
                if ($code===200 && $body) {
                    $arr = $parseRss($body);
                    if (!empty($arr)) {
                        $a = $arr[0];
                        $items[$rss['key']] = $norm(
                            $a['title'] ?? '',
                            $rss['source'],
                            $a['url'] ?? '',
                            $a['image'] ?? '',
                            $a['published'] ?? null
                        );
                    } else { $errors[]=$rss['key'].": RSS vacío"; }
                } else { $errors[]=$rss['key'].": HTTP {$code} {$err}"; }
                if (count($items) >= 8) break;
            }
        }

        // Completar imágenes ausentes extrayendo de la página destino
        if (!empty($items)) {
            foreach ($items as $k => $it) {
                if (!isset($it['image']) || $it['image']==='') {
                    $link = $it['url'] ?? '';
                    if (!$link) continue;
                    list($code,$body,$ct,$err) = $fetchCurl($link, 10, [
                        'Accept: text/html,application/xhtml+xml;q=0.9,*/*;q=0.5'
                    ]);
                    if ($code===200 && is_string($body) && $body!=='') {
                        $media = $extractMediaFromHtml($body, $link);
                        if ($media) {
                            $items[$k]['image'] = $media;
                        }
                    }
                }
            }
        }

        // Caché lectura
        $usedCache = false;
        if (empty($items) && is_file($cacheFile)) {
            $raw = @file_get_contents($cacheFile);
            if ($raw) {
                $snap = json_decode($raw, true);
                if (is_array($snap) && !empty($snap['items'])) {
                    $items = $snap['items'];
                    $usedCache = true;
                }
            }
        }

        // Fallback estático
        if (empty($items)) {
            $items = [
                "fallback1" => $norm("Sección Salud", "CNN Español", "https://cnnespanol.cnn.com/category/salud/", "", null),
                "fallback2" => $norm("Salud y Bienestar", "El País", "https://elpais.com/salud-y-bienestar/", "", null),
                "fallback3" => $norm("OPS/PAHO - Noticias", "PAHO/OPS", "https://www.paho.org/es/noticias", "", null),
            ];
        }

        // Cache escritura
        if (!$usedCache && !empty($items)) {
            $snapshot = [
                "success"   => true,
                "items"     => $items,
                "errors"    => $errors,
                "timestamp" => $now
            ];
            @file_put_contents($cacheFile, json_encode($snapshot, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));
        }

        $this->jsonResponse([
            "success"   => true,
            "items"     => $items,
            "errors"    => $errors,
            "timestamp" => $now
        ]);
    }
}
?>

