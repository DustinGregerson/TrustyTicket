//Author Dustin Gregerson

//Most of these expreshions expect that the tag is on one line and there are not nested elements
//The Create Formmated HTML Array function provides this
const ALL_WHITE_SPACE=/^\s*$/;          //used to find strings that contain nothing but whitespace
const OPENING_TAG=/^<[^\/!][^>]*>$/;    //used to find an opening tag
const TAG_ELEMENT=/\w+\b/;              //used to find the element in the tag 
const TEXT=/^[^<].*[^>]$/;              //used to find text
const CLOSING_TAG=/^<\/[^>]*>$/;        //used to find a closing
const HEAD_START_TAG=/^<head\b.*>/;     //used to find a head opening tag
const HEAD_END_TAG=/^<\/head>/          //used to find a head ending tag
const HEADER_START_TAG=/^<header\b>/;   //used to find a header opening tag
const HEADER_END_TAG=/<\/header>/;      //used to find a header closing tag
const SCRIPT_OPEN_TAG=/^<script\b.*>/;  //used to find a script opening tag
const SCRIPT_CLOSE_TAG=/^<\/script>/;   //used to find a script closing tag
const SINGLE_LINE_COMMENT=/^<!--.*$/;   //used to find a comment line
const ATTRIBUTES=/(\s\w+="\w*"){1}/g;   //used to find all of the attributes in the opening tag. NOTE: may not be needed
//Do not think in terms of what may be but what could be.

//the head is removed because css can not affect the head
//the scripts are removed because css can not affect the script
$(document).ready(function() {
    //first remove all of the head and any scripts that are in the document
    let formmatedHtmlArray=removeUneededSections(CreateFormattedHTMLArray());
    console.log(formmatedHtmlArray);
    //creates the parent element dom object
    let parentNode=CreateDomObjectWithIndex(formmatedHtmlArray,0);
    parentNode["depth"]=0;
    //from the parent element dom object find all of the nested children
    let tree=createDOMTree(formmatedHtmlArray,parentNode);
    console.log(tree);

});
//__________________________________________FROMMATED ARRAY SECTION__________________________________________________________________________
//Returns a newly formated array with all of the html and text elements on the page
//The opening and closing tags are placed within there own index as is the text
function CreateFormattedHTMLArray(){
    let rawHtml=document.documentElement.outerHTML;
    let alteredHtml="";

    for( i=0;i<rawHtml.length;i++){
        if(rawHtml[i]==">"){
            alteredHtml+=rawHtml[i]+"\n";
        }
        else{
            alteredHtml+=rawHtml[i];
        }
    }
    rawHtml="";
    for( i=0;i<alteredHtml.length;i++){
        if(alteredHtml[i]=="<"){
            rawHtml+="\n"+alteredHtml[i];
        }
        else{
            rawHtml+=alteredHtml[i];
        }
    }
    alteredHtml="";

    let split=rawHtml.split("\n");
    rawHtml="";
    let final=[];
    for(i=0;i<split.length;i++){
            split[i].trim();
            if(!split[i].match(ALL_WHITE_SPACE)){
                final.push(split[i]);
            }
    }
    return final;
}

//removes the header from an html array
function removeSingleLineComments(htmlArray){
    let length=htmlArray.length;
    let htmlWithoutComments=[];
    for(i=0;i<length;i++){
        if(htmlArray[i].match(SINGLE_LINE_COMMENT)){
            continue;
        }
        else{
            htmlWithoutComments.push(htmlArray[i]);
        }
    }
    return htmlWithoutComments;
}

function removeHead(htmlArray){
    let length=htmlArray.length;
    let start=false;
    let htmlWithoutHead=[];
    for(i=0;i<length;i++){
        if(htmlArray[i].match(HEAD_START_TAG)){
            start=true;
            continue;
        }
        else if(htmlArray[i].match(HEAD_END_TAG)){
            start=false;
            continue;           
        }
        if(!start){
            htmlWithoutHead.push(htmlArray[i]);
        }
    }
    return htmlWithoutHead;
}
function removeScripts(htmlArray){
    let length=htmlArray.length;
    let start=false;
    let htmlWithoutHead=[];
    for(i=0;i<length;i++){
        if(htmlArray[i].match(SCRIPT_OPEN_TAG)){
            start=true;
            continue;
        }
        else if(htmlArray[i].match(SCRIPT_CLOSE_TAG)){
            start=false;
            continue;           
        }
        if(!start){
            htmlWithoutHead.push(htmlArray[i]);
        }
    }
    return htmlWithoutHead;
}

function removeUneededSections(htmlArray){
    return(removeSingleLineComments(removeScripts(removeHead(htmlArray))));
}
//________________________________________DOM TREE AND DOM OBJECTS___________________________________________________________________________
//finds the first element in the array and then searchs for the closing tag for the element. If another element is found with the
//same name then the element counter will ++ if the a closing tag for the element or elements is found the the element counter
//will --. when the element counter reachs zero the ending tag for the first element has been found.
function createDOMTree(htmlArray,parent){
    if(hasChildren(htmlArray,parent)){
        //finds all the children in the parent and adds them to the parent
        CreateDomObjectChildren(htmlArray,parent);
        findTextAndChildPosFromParent(htmlArray,parent);
    }
    else{
        //no children no tree
        return null;
    }
    let column=[];
    //every index reperesnts a column
    //pos 0 is the first child of the parent
    //childrenLength is the number of children nested in the parent
    column.push(
            {
            "childrenLength":parent["children"].length-1,
            "pos":0
            }
    );
    let currentChild=parent["children"][0];

    //add a column for every new group of children
    //remove the column if the positon points to the last child and the child has no children.
    //when the column array is empty the tree has been created.
    let findCurrentColumn=false;
    let trip=0;
    while(column.length>0){

        if(column[column.length-1]["pos"]>column[column.length-1]["childrenLength"]){
            column.pop();
            if(column.length==0){
                break;
            }

            column[column.length-1]["pos"]++;
            findCurrentColumn=true;

            continue;
        }


        //Searches back through the tree to find the current child relative to the columns
        if(findCurrentColumn){
            currentChild=parent["children"][column[0]["pos"]];
            for(i=1;i<column.length;i++){
                currentChild=currentChild["children"][column[i]["pos"]];
            }
        }

        //columns of children are pushed onto the array and poped back off if all the children in the column have been checked
        //to see if they have children of there own and there are none.
        

        if(hasChildren(htmlArray,currentChild)){
            //new children added to the current child
            CreateDomObjectChildren(htmlArray,currentChild);

            //a new column is added with a 0 position to point to the first child of the new children
            column.push(
                {
                "childrenLength":currentChild["children"].length-1,
                "pos":0
                }
            );
            findTextAndChildPosFromParent(htmlArray,currentChild);
            //the current child is now the first child of the children that were added to the current child
            currentChild=currentChild["children"][0];
        }
        else{
            findTextAndChildPosFromParent(htmlArray,currentChild);
            findCurrentColumn=true;
            column[column.length-1]["pos"]++;
        }
        if(trip==100000){
            console.log("tripped 1000")
        }
        trip++;
    }
    return parent;
    //then remove the header and scripts from the page
}

function CreateDomObjectWithoutIndex(htmlArray){
    let startIndex=0;
    //search for the next opening tag
    while(!htmlArray[startIndex].match(OPENING_TAG)&&startIndex!=htmlArray.length){
        startIndex++;
    }
    //get the element name from the opening tag
    let element=htmlArray[startIndex].match(TAG_ELEMENT)[0];
    let regex= new RegExp(`</${element}\\b`);
    let length=htmlArray.length;
    let elementCounter=1;

    for(i=0;i<length;i++){
        //if the line is text ignore it
        if(htmlArray[i][0]!="<"){
            continue;
        }
        let match=null;
        match=htmlArray[i].match(regex);

        if(match==null){
            continue;
        }
        if(element==htmlArray[i].match(TAG_ELEMENT)&&htmlArray[i].match(OPENING_TAG)){
            elementCounter++;
        }
        if(element==htmlArray[i].match(TAG_ELEMENT)&&htmlArray[i].match(CLOSING_TAG)){
            elementCounter--;
        }
        if(elementCounter==0){
            return buildDOMObject(element,startIndex,i);
        }
    }
    
}
//Same as the function above however the starting index is passed
function CreateDomObjectWithIndex(htmlArray,index){
    let startIndex=index;
    //search for the next opening tag
    while(!htmlArray[startIndex].match(OPENING_TAG)&&startIndex!=htmlArray.length){
        startIndex++;
    }
    //get the element name from the opening tag
    let element=htmlArray[startIndex].match(TAG_ELEMENT)[0];
    let regex= new RegExp(`^<\/?${element}\\b`);
    let length=htmlArray.length;
    let elementCounter=1;
    let lineElements=["area","base","br","col","embed","hr","img","input","link","meta","param","source","track","wbr"];



    if(lineElements.includes(element)){
        return buildDOMObject(element,startIndex,startIndex);
    }
    else{
    for(i=startIndex+1;i<length;i++){
            //if the line is text ignore it
            
            if(htmlArray[i][0]!="<"){
                continue;
            }
            let match=null;
            match=htmlArray[i].match(regex);

            if(match==null){
                continue;
            }
            
            if(element==htmlArray[i].match(TAG_ELEMENT)&&htmlArray[i].match(OPENING_TAG)){
                elementCounter++;
            }
            if(element==htmlArray[i].match(TAG_ELEMENT)&&htmlArray[i].match(CLOSING_TAG)){
                elementCounter--;
            }
            if(elementCounter==0){
                return buildDOMObject(element,startIndex,i);
            }
        }
    }
    
}
function hasChildren(htmlArray,parent){
    let startIndex=parent["start"];
    let endIndex=parent["end"];
    if(startIndex+1!=endIndex){
        for(i=startIndex+1;i<endIndex;i++){
            if(htmlArray[i].match(OPENING_TAG)){
                return true;
            }
        }
        return false;
    }
    else{
        return false;
    }
}
function CreateDomObjectChildren(htmlArray,parent){
    let startIndex=parent["start"];
    let endIndex=parent["end"];

    if(startIndex+1!=endIndex){
        for(i=startIndex+1;i<endIndex;i++){
            if(htmlArray[i].match(OPENING_TAG)){
                let child=CreateDomObjectWithIndex(htmlArray,i);
                child["depth"]=parent["depth"]+1;
                addChild(parent,child);
                startIndex=child["end"]+1;
            }
        }
    }
}
function findTextAndChildPosFromParent(htmlArray,parent){
    let children=parent["children"];
    let childrenNum=children.length;
    let pos=0;
    if(parent["start"]==4){
        console.log("hit");
    }
    for(i=parent["start"]+1;i<parent["end"];i++){
        if(childrenNum){
            for(ii=0;ii<childrenNum;ii++){
                //I is past the current child
                if(i>=children[ii]["end"]){
                    continue;
                }
                //I is between the childs start position and ending position
                if(i>=children[ii]["start"]&&i<=children[ii]["end"]){
                    i=children[ii]["end"];
                    parent["childPos"].push(pos);
                    pos++;
                    break;
                }
                //I is between the parent and the child or I is between two children within the parent and matches a text line
                if(i<children[ii]["start"]&&htmlArray[i].match(TEXT)){
                    parent["textPos"].push(pos);
                    parent["text"].push(htmlArray[i]);
                    pos++;
                    break;
                }
            }
        }
        else{
            parent["textPos"].push(pos);
            parent["text"].push(htmlArray[i]);
            pos++;
        }
    }
}

function buildDOMObject(element,openingIndex,closingIndex){
    let domObject=
    {
        "element":element,
        "start":openingIndex,
        "end":closingIndex,
        "depth":0,
        "attributes":[],
        "text":[],
        "textPos":[],
        "children":[],
        "childPos":[]
        
    }
    return domObject;
}
function addAttribute(DomObject,attribute){
    DomObject["attributes"].push(attribute);
}
//____________________________________________________
//Both of these functions must share the same pos variable 
function addChild(ParentDomObject,ChildDomObject){
    ParentDomObject["children"].push(ChildDomObject);
}
function addText(ParentDomObject,text,pos){
    ParentDomObject["text"].push(text);
    ParentDomObject["textPos"].push(pos);
}
//_______________________________________________________________________________________________________________________________
