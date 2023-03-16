const serverForm = document.getElementById("serverForm");
const serverInput = document.getElementById("serverInput");

serverForm.addEventListener("submit",()=>{

    if(serverInput.value.length == 0){
        event.preventDefault();
        return alert("作成するサーバー名を入力してください");
    }else if(serverInput.value.length > 10){
        event.preventDefault();
        return alert("作成するサーバー名は10文字以内にしてください");
    }
});