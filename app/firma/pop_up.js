function styleButton(button) {
	button.style.marginTop = "15px";
  button.style.marginRight = "15px";
  button.style.marginLeft = "15px";
	button.style.border = "0";
	button.style.lineHeight = "1.8";
	button.style.padding = "0 20px";
	button.style.fontSize = "1.1rem";
	button.style.textAlign = "center";
	button.style.color = "#fff";
	button.style.textShadow = "1px 1px 1px #000";
	button.style.borderRadius = "10px";
	button.style.backgroundColor = "cornflowerblue";
	button.style.backgroundImage = "linear-gradient( to top left, rgba(0, 0, 0, 0.2), rgba(0, 0, 0, 0.2) 30%, rgba(0, 0, 0, 0) )";
	button.style.boxShadow = "inset 2px 2px 3px rgba(255, 255, 255, 0.6), inset -2px -2px 3px rgba(0, 0, 0, 0.6)";
}

function styleDialog(dialog) {
    dialog.style.borderRadius = "5px";
    dialog.style.backgroundColor = "white";
    dialog.style.border = "1px solid gray";
    dialog.style.padding = "15px";
    dialog.style.position = "fixed";
    dialog.style.top = "50%";
    dialog.style.left = "50%";
    dialog.style.transform = "translate(-50%, -50%)";
    dialog.style.zIndex = "9999";
    dialog.style.textAlign = "center";
}

function customAlert(msg) {
    var dialog = document.createElement("div");
  
    var message = document.createElement("p");
    message.innerText = msg;
    dialog.appendChild(message);
    
    styleDialog(dialog);
    
    message.style.fontWeight ="bold";
    message.style.color = "#424949";
  
    var buttonOK = document.createElement("button");
    buttonOK.innerText = "OK";
  
    styleButton(buttonOK);
  
    buttonOK.onclick = function() {
      dialog.remove();
    };
  
    dialog.appendChild(buttonOK);
    
    document.body.appendChild(dialog);
  }


  function customAlertOpenWin(msg,adr_url) {
    var dialog = document.createElement("div");
  
    var message = document.createElement("p");
    message.innerText = msg;
    dialog.appendChild(message);
    
    styleDialog(dialog);
    
    message.style.fontWeight ="bold";
    message.style.color = "#424949";
  
    var buttonOK = document.createElement("button");
    buttonOK.innerText = "OK";
  
    styleButton(buttonOK);
  
    buttonOK.onclick = function() {
      dialog.remove();
      window.open(adr_url,"_self");
    };
  
    dialog.appendChild(buttonOK);
    
    document.body.appendChild(dialog);
  }

  

  function customAlertWinClose(msg, callback){
    // Create a modal
    let modal = document.createElement("div");
    modal.setAttribute("id", "myModal");
    modal.setAttribute("style", "display: none; position: fixed; z-index: 1; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgb(0,0,0); background-color: rgba(0,0,0,0.4);");

    // Create a modal content
    let modalContent = document.createElement("div");

    styleDialog(modalContent);
    // Create a close button
    let closeBtn = document.createElement("button");
    closeBtn.innerHTML = "OK";
    styleButton(closeBtn);
    closeBtn.onclick = function() {
        modal.style.display = "none";
        if(callback) callback();
    }

    // Create a text node
    let textNode = document.createTextNode(msg);


    var message = document.createElement("p");
    message.innerText = msg;
    message.style.fontWeight ="bold";
    message.style.color = "#424949";
    modalContent.appendChild(message);
    modalContent.appendChild(closeBtn);
    modal.appendChild(modalContent);
    document.body.appendChild(modal);

    // Display the modal
    modal.style.display = "block";
}




/*
function customAlertWinClose(msg, callback) {
  // Create a modal element
  let modal = document.createElement("div");
  modal.setAttribute("id", "myModal");

  // Create a modal content element
  let modalContent = document.createElement("div");

  // Style the modal and modal content
  modal.style.cssText = `
    display: none;
    position: fixed;
    z-index: 1;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0, 0, 0, 0.4);
  `;
  modalContent.style.cssText = `
    background-color: #fefefe;
    margin: 15% auto;
    padding: 20px;
    border: 1px solid #888;
    width: 80%;
  `;

  // Create a close button element
  let closeBtn = document.createElement("button");
  closeBtn.innerText = "OK";

  // Style the close button (consider using a separate function for reusability)
  closeBtn.style.cssText = `
    background-color: #007bff; 
    color: #fff;
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
  ;

  // Create a text node for the message
  let textNode = document.createTextNode(msg);

  // Append elements to the modal content
  modalContent.appendChild(textNode);
  modalContent.appendChild(closeBtn);

  // Append modal content to the modal
  modal.appendChild(modalContent);

  // Append the modal to the document body
  document.body.appendChild(modal);

  // Display the modal
  modal.style.display = "block";

  // Close button click handler
  closeBtn.addEventListener("click", () => {
    modal.style.display = "none";
    if (callback) {
      callback();
    }
  });
}*/