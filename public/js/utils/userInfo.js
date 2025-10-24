export const updateUserInfo = (data) => {
    const branchName = document.getElementById('branchName');
    const navUserName = document.getElementById('navUserName');
    const navRoleName = document.getElementById('navRoleName');
    const dropUserName = document.getElementById('dropUserName');
    
    branchName.textContent = data.branch_name;
    navUserName.textContent = data.user_name;
    dropUserName.textContent = data.user_name;
    navRoleName.innerText = data.role_name;
};