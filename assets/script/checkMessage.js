const messageForm = document.getElementById("messageForm");
const messageInput = document.getElementById("messageInput");

messageForm.addEventListener("submit",()=>{

    if(messageInput.value.length == 0){
        event.preventDefault();
        return alert("メッセージを入力してください");
    }else if(messageInput.value.length > 300){
        event.preventDefault();
        return alert("送信するメッセージはは300文字以内にしてください");
    }
});