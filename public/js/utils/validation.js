// Validación de campos vacíos
export function isEmpty(value) {
    return value.trim().length === 0;
}

// Validación de correo electrónico
export function isValidEmail(email) {
    const emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;
    return emailPattern.test(email);
}

// Validación de longitud mínima
export function hasMinLength(value, minLength) {
    return value.trim().length >= minLength;
}

// Validación de contraseñas
export function isValidPassword(password) {
    const minLength = 8;
    const hasLetter = /[A-Za-z]/;
    const hasDigit = /\d/;
    return password.length >= minLength && hasLetter.test(password) && hasDigit.test(password);
}

// Comparación de contraseñas
export function arePasswordsMatching(password, confirmPassword) {
    return password === confirmPassword;
}

// Validación de número
export function isNumber(value) {
    return !isNaN(value);
}

// Validación de fecha
export function isValidDate(date) {
    return !isNaN(Date.parse(date));
}

// Validación de fecha futura
export function isFutureDate(date) {
    const today = new Date();
    const inputDate = new Date(date);
    return inputDate > today;
}

// Validación de número de teléfono
export function isValidPhoneNumber(phoneNumber) {
    const phonePattern = /^[0-9]{8}$/;  // Número de teléfono de 10 dígitos
    return phonePattern.test(phoneNumber);
}
