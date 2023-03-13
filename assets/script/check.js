const sendForm = document.getElementById("sendForm");
const sendBInput = document.getElementById("sendInput");
const sendButton = document.getElementById("sendButton");

sendForm.addEventListener("input",()=>{
    const isRequired = sendForm.checkValidity();

    if(isRequired){
        if(sendInput.value.length > 300){
            return alert("入力文字は300文字以内にしてください");
        }
        sendButton.disabled = false;
    }
});