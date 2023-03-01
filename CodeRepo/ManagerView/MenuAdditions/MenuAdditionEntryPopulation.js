
var RootEntryField;
var LabelIncrement = 0; // testing label
var ExistingCategoriesArray;
var ExistingItemsArray;
var Selected;
var RootEntryType;
var RootItemType;

function GetTypeDataToInput() {
    Selected = document.getElementById('category-or-item').value;
 
    if (Selected == 'category') {
        IsSubCategory();
    }
    else if (Selected == 'item') {
        GetItemEntryCategory();
    }
    // else if (Selected == 'mod') {
    //     GetEntryPopNum();
    // }
    else {
        alert('Please Select An Option');
    }
}


// ************ START MODS ****************



function GetModAssociationToItem() {
    document.getElementById('header-text').style.display = 'none';
    document.getElementById('selection-category-or-item').style.display = 'none';
    document.getElementById('get-items-btn').style.display = 'none';
    document.getElementById('selection-item-type').style.display = 'block';

    RootItemType = document.getElementById('root_item-type')
    var ExistingItemsString = document.getElementById('list-of-items').innerHTML;
    ExistingItemsString = ExistingItemsString.substring(0, ExistingItemsString.length-1);
    ExistingItemsArray = ExistingItemsString.split(',');

    for (let i=0; i<ExistingItemsArray.length; i++) {
        var ItemsOption = document.createElement('option');
        ItemsOption.setAttribute('value', ExistingItemsArray[i]);
        ItemsOption.appendChild(document.createTextNode(ExistingItemsArray[i]));
        RootItemType.appendChild(ItemsOption);
    }
}

function GetEntryPopNum() {
    document.getElementById('selection-item-type').style.display = 'none';
    ShowEntryNumSelection();
    document.getElementById('entry-count-chosen').style.display = 'none';
    document.getElementById('entry-count-chosen_mod').style.display = 'block';
}

function ModPopulation() {

    // Init RootEntryType
    RootEntryType = 'mod';

    document.getElementById('selection-populatable-fields').style.display = 'none';
    document.getElementById('form-for-entries').style.display = 'block';
    
    var NumFieldsSelected = document.getElementById('entry-count').value;
    RootEntryField = document.getElementById('root_entry-field');
    document.getElementById('header-text').style.display = 'none';

    // Entry Header Showing Name Of Type
    var CategoryChosen = document.createElement('h4');
    CategoryChosen.setAttribute('style', 'text-decoration:underline;');
    CategoryChosen.appendChild(document.createTextNode(document.getElementById('root_item-type').value));
    RootEntryField.appendChild(CategoryChosen);
    for (let i=0; i<NumFieldsSelected; i++) {
 
        // LABEL FOR TITLE
        var PromptTitle = document.createElement('label'); // testing label
        PromptTitle.setAttribute('for', ('entry-title' + LabelIncrement));  // testing label
        PromptTitle.appendChild(document.createTextNode('Title:'));
        RootEntryField.appendChild(PromptTitle);

        // INPUT FOR TITLE
        var EntryFieldTitle = document.createElement('input');
        EntryFieldTitle.setAttribute('id', ('entry-title' + LabelIncrement)); // testing label
        EntryFieldTitle.setAttribute('type', 'text');
        EntryFieldTitle.setAttribute('class', 'input-field-title');
        EntryFieldTitle.setAttribute('name','entry-title' + LabelIncrement);
        RootEntryField.appendChild(EntryFieldTitle);

        // INPUT FOR MANDATORY/OPTIONAL
        var EntryMandatoryOptional = document.createElement('select');
        EntryMandatoryOptional.setAttribute('id', 'mandatory-optional');
        EntryMandatoryOptional.setAttribute('class', 'input-field-title');
        EntryMandatoryOptional.setAttribute('name', 'entry-mand_opt' + LabelIncrement);
        var Opt = document.createElement('option');
        Opt.setAttribute('value', '4');
        Opt.appendChild(document.createTextNode('Optional'));
        EntryMandatoryOptional.appendChild(Opt);

        var Mand = document.createElement('option');
        Mand.setAttribute('value', '2');
        Mand.appendChild(document.createTextNode('Mandatory'));
        EntryMandatoryOptional.appendChild(Mand);
        RootEntryField.appendChild(EntryMandatoryOptional);


        // HIDDEN PASSTHROUGHS:
        // ADD RootEntryType TO SHOW THAT A MOD IS PASSED
        var PassMod = document.createElement('input');
        PassMod.setAttribute('id', 'mod');
        PassMod.setAttribute('type', 'text');
        PassMod.setAttribute('value', 'mod');
        PassMod.setAttribute('name', 'entry-type-form');
        PassMod.setAttribute('style', 'display:none;');
        RootEntryField.appendChild(PassMod);

        // ADD PASSTHROUGH OF MOD TITLE TO GET QUICKCODE
        var PassItemTitle = document.createElement('input');
        PassItemTitle.setAttribute('id', 'item-title');
        PassItemTitle.setAttribute('type', 'text');
        PassItemTitle.setAttribute('value', document.getElementById('root_item-type').value);
        PassItemTitle.setAttribute('name', ('entry-item-title' + LabelIncrement));
        PassItemTitle.setAttribute('style', 'display: none;');

        document.getElementById('num-of-entries-form').setAttribute('name', 'numEntries');
        document.getElementById('num-of-entries-form').setAttribute('value', (NumFieldsSelected));

        // ADD LINE BREAK
        RootEntryField.appendChild(document.createElement('br'));

        // ADD HORIZONTAL RULE
        RootEntryField.appendChild(document.createElement('hr'));

        LabelIncrement += 1;
    }
    //PopulateNameAttributesInForm();
}


// ************ END MODS ****************



// Add function to select category if the category type is a sub category
function IsSubCategory() {
    // Change header-text
    document.getElementById('header-text').innerHTML = 'Root Or Sub Category Addition Selection';

    // Hide and Reveal Elements
    document.getElementById('get-items-btn').style.display = 'none';
    document.getElementById('selection-category-or-item').style.display = 'none';
    document.getElementById('root_or_sub_div').style.display = 'block';
    

    // ADD SELECTION OF ROOT OR SUB HERE. IF-ELSE STATEMENT TO DETERMINE WHETHER
    //  OR NOT GetItemEntryCategory() NEEDS TO BE CALLED OR NOT. IF ROOT CATEGORY,
    //  THEN ROOT NEEDS TO BE THE ASSOCIATION MADE, OTHERWISE, SELECTED CATEGORY
    //  FROM GetItemEntrySelection() WILL BE THE ASSOCIATED CATEGORY.
    var CategoryRoot = document.createElement('option');
    CategoryRoot.setAttribute('value', 'root');
    CategoryRoot.appendChild(document.createTextNode('Root/Main Category'));
    document.getElementById('root_or_sub_input').appendChild(CategoryRoot);
    
    var CategorySub = document.createElement('option');
    CategorySub.setAttribute('value', 'sub');
    CategorySub.appendChild(document.createTextNode('Sub Category'));
    document.getElementById('root_or_sub_input').appendChild(CategorySub); 

    // Call function to get the choice and operate based on that choice
    //  through the button on MenuAdditionView.php within the root_or_sub_div
    //  div with that ID. The eventListener for that button is within the view.
}

function GetCategoryChoice() {
    document.getElementById('root_or_sub_div').style.display = 'none';
    
    if (document.getElementById('root_or_sub_input').value == 'root') {
        document.getElementById('root_entry-type').value = 'root';
        ShowEntryNumSelection();
    }
    else if (document.getElementById('root_or_sub_input').value == 'sub') {
        GetItemEntryCategory();
    }
    else {
        alert('You Have To Choose The Category Type');
        // Refresh
        location.reload(true);
    }
}

function GetItemEntryCategory() {
    // Change header-text
    if (Selected == 'category') {
        document.getElementById('header-text').innerHTML = 'Select A Category To Add A Sub Category To';
    }
    else if (Selected == 'item') {
        document.getElementById('header-text').innerHTML = 'Select A Category To Add A Menu Item To';
    }

    // Hide first block, show this block if 'category' is chosen
    document.getElementById('get-items-btn').style.display = 'none';
    document.getElementById('selection-category-or-item').style.display = 'none';
    document.getElementById('selection-entry-type').style.display = 'block';
    
    RootEntryType = document.getElementById('root_entry-type');
    var ExistingCategoriesString = document.getElementById('list-of-categories').innerHTML;
    ExistingCategoriesString = ExistingCategoriesString.substring(0, ExistingCategoriesString.length-1);
    ExistingCategoriesArray = ExistingCategoriesString.split(',');

    for (let i=0; i<ExistingCategoriesArray.length; i++) {
        var CategoriesOption = document.createElement('option');
        CategoriesOption.setAttribute('value', ExistingCategoriesArray[i]);
        CategoriesOption.appendChild(document.createTextNode(ExistingCategoriesArray[i]));
        RootEntryType.appendChild(CategoriesOption);
    }
}

function ShowEntryNumSelection() {
    // Change header-text
    if (Selected == 'category') {
        document.getElementById('header-text').innerHTML = 'Choose The Number Of Categories To Add';
    }
    else if (Selected == 'item') {
        document.getElementById('header-text').innerHTML = 'Choose The Number Of Items To Add';
    }
    else {
        document.getElementById('header-text').innerHTML = "Choose The Number Of Modification Items To Add";
    }

    // Hide And Reveal Elements
    document.getElementById('selection-category-or-item').style.display = 'none';
    document.getElementById('selection-entry-type').style.display = 'none';
    document.getElementById('selection-populatable-fields').style.display = 'block';
}

function EntryPopulation() {                                                                                                // NOTE:
                                                                                            // ************************************************************************************
                                                                                            // *   Hide the dropdown for number of entries to choose from. This keeps the          *
                                                                                            // *     number of fields below 10 and will/should have a button to unhide the         *
                                                                                            // *     entries number selection after pressing the button ('Clear' is likely name)   *
                                                                                            // *     and if they have submitted their new entries, reload the page to redisplay    *
                                                                                            // *     that enty number dropdown and button. The hiding will be by hiding the <div>. *
                                                                                            // ************************************************************************************
    document.getElementById('selection-populatable-fields').style.display = 'none';
    document.getElementById('header-text').style.display = 'none';
    document.getElementById('form-for-entries').style.display = 'block';

    var NumFieldsSelected = document.getElementById('entry-count').value;
    RootEntryField = document.getElementById('root_entry-field');
  
    

    // Entry Header Showing Name Of Type
    var CategoryChosen = document.createElement('h4');
    CategoryChosen.setAttribute('style', 'text-decoration:underline;');
    // if (!RootEntryType.value) {
    CategoryChosen.appendChild(document.createTextNode('RootEntryType.value'));
    // }
    // else {
    //     CategoryChosen.appendChild(document.createTextNode('Root Category'));
    // }
    RootEntryField.appendChild(CategoryChosen);

    // ADD HORIZONTAL RULE
    RootEntryField.appendChild(document.createElement('hr'));

    for (let i=0; i<NumFieldsSelected; i++) {

        // LABEL FOR TITLE
        var PromptTitle = document.createElement('label'); // testing label
        PromptTitle.setAttribute('for', ('entry-title' + LabelIncrement));  // testing label
        PromptTitle.appendChild(document.createTextNode('Title:'));
        RootEntryField.appendChild(PromptTitle);

        // INPUT FOR TITLE
        var EntryFieldTitle = document.createElement('input');
        EntryFieldTitle.setAttribute('id', ('entry-title' + LabelIncrement)); // testing label
        EntryFieldTitle.setAttribute('type', 'text');
        EntryFieldTitle.setAttribute('class', 'input-field-title');
        EntryFieldTitle.setAttribute('name','entry-title' + LabelIncrement);
        RootEntryField.appendChild(EntryFieldTitle);

        // SEND CATEGORY
        var EntryCategory = document.createElement('input');
        EntryCategory.setAttribute('name', 'entry-cat');
        if (document.getElementById('root_or_sub_input').value == 'root') {
            EntryCategory.setAttribute('value', 'root');
        }
        else {
            EntryCategory.setAttribute('value', RootEntryType.value);
        }
        EntryCategory.setAttribute('style', 'display:none;');
        RootEntryField.appendChild(EntryCategory);

        // if (!ExistingCategoriesArray.includes(document.getElementById('root_entry-type').value)) {
        if (document.getElementById('category-or-item').value == 'item') {
            // ADD BREAKLINE x2
            RootEntryField.appendChild(document.createElement('br'));
            RootEntryField.appendChild(document.createElement('br'));

            // LABEL FOR PRICE
            var PromptPrice = document.createElement('label'); // testing label
            PromptPrice.setAttribute('for', ('entry-price' + LabelIncrement));    // testing label
            PromptPrice.appendChild(document.createTextNode('Price:'));
            RootEntryField.appendChild(PromptPrice);

            // INPUT FOR PRICE
            var EntryFieldPrice = document.createElement('input');  //
            EntryFieldPrice.setAttribute('id', ('entry-price' + LabelIncrement)); //testing label
            EntryFieldPrice.setAttribute('type','text');
            EntryFieldPrice.setAttribute('class','input-field-price');
            EntryFieldPrice.setAttribute('name','entry-price' + LabelIncrement);
            RootEntryField.appendChild(EntryFieldPrice);

            // ADD BREAKLINE 
            RootEntryField.appendChild(document.createElement('br'));           

        }

        // ADD HORIZONTAL RULE
        RootEntryField.appendChild(document.createElement('hr'));
            
        LabelIncrement += 1;
        }
        PopulateNameAttributesInForm();
    }


function PopulateNameAttributesInForm() {

    // Get number of entries chosen to loop through, as well as the entry type
    var EntryType = document.getElementById('category-or-item').value;
    var NumOfEntries = document.getElementById('entry-count').value;


    // Add a hidden input for the chosen entry type (Category, Menu Item, Modifications), as well as number of entries
    document.getElementById('entry-type-from_root').setAttribute('name', 'entry-type-form');
    document.getElementById('entry-type-from_root').setAttribute('value', (EntryType));
    
    document.getElementById('num-of-entries-form').setAttribute('name', 'numEntries');
    document.getElementById('num-of-entries-form').setAttribute('value', (NumOfEntries));
    

    // Problem Below


    if (EntryType == 'item') {
        for (var i=0; i<NumOfEntries; i++) {
            document.getElementById(('entry-title' + i)).setAttribute.name = ('entry-title');
            document.getElementById(('entry-price' + i)).setAttribute.name = ('entry-price');
        }
    }
    else if (EntryType == 'category') {
        for (var i=0; i<NumOfEntries; i++) {
            document.getElementById('entry-title' + i).setAttribute.name = ('entry-title');
        }
    }
    else {
        for (var i=0; i<NumOfEntries; i++) {
            document.getElementById('entry-title' + i).setAttribute.name = ('entry-title');
        }
    }

    // Unhide submission button
    document.getElementById('submit-form-btn').style.display = 'block';

}