//This function should select a ticket item, while deselecting any other items that are already selected.
window.selectTicketItem = function(id){
    var oldSelectedItems = document.getElementsByClassName("selected");
    /*this iterates through the list returned, if there is no case where multiple items are selected concurrently,
    you can just use oldSelectedItems[0].classList.remove("selected"); instead*/
    for(let i = 0; i < oldSelectedItems.length; i++){
        oldSelectedItems[i].classList.remove("selected");
    }
    var newSelectedItem = document.getElementById(id);
    newSelectedItem.classList.add("selected");
    window.stateChanged();
};

//this function should remove a ticket item from the list. It does not know if the item exists in the database, that will have to be covered elsewhere.

window.removeTicketItem = function(){
    //remove the event listener that called this function
    this.removeEventListener("onclick", removeTicketItem);
    //determine the ticket item related to the remove button that was clicked.
    var theItem = this.parentElement;
    //remove the event listener for selecting the parent element
    theItem.removeEventListener("onclick", selectTicketItem);
    //remove the parent element
    theItem.remove();
    window.stateChanged();
};
