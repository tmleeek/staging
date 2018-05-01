

function removePackage(packageId)
{
    var target = document.getElementById('package' + packageId);

    var package_index = parseInt(document.getElementById('index_package').value);

    document.getElementById('index_package').value = package_index - 1;

    document.getElementById('packages_container').removeChild(target);
}

function updateFields(packageID){
    var selectInput = $('colissimo_parceltype['+packageID+']');
    var value = selectInput.value;
    var inputwidth = document.getElementsByName("colissimo[package]["+packageID+"][width]")[0];
    var inputheight = document.getElementsByName("colissimo[package]["+packageID+"][height]")[0];
    var inputdiam = document.getElementsByName("colissimo[package]["+packageID+"][diam]")[0];
    var thwidth = document.getElementById("width["+packageID+"]");
    var thheight = document.getElementById("height["+packageID+"]");
    var thdiam = document.getElementById("diam["+packageID+"]");
    if(value == 1){
        inputheight.parentNode.style.display = '';
        inputwidth.parentNode.style.display = '';
        thwidth.style.display = '';
        thheight.style.display = '';
        thdiam.style.display = 'none';
        inputdiam.parentNode.style.display = 'none';
    }else if(value == 2){
        inputheight.parentNode.style.display = 'none';
        inputwidth.parentNode.style.display = 'none';
        thwidth.style.display = 'none';
        thheight.style.display = 'none';
        thdiam.style.display = '';
        inputdiam.parentNode.style.display = '';
    }
}

function updateRegate(needle, packageID){
    var regateth = document.getElementById("regateth["+packageID+"]");
    var regatetd = document.getElementById("regatetd["+packageID+"]");
    var haystack = ['A2P', 'MRL', 'CIT', 'BPR', 'ACP', 'CDI', 'CMT', 'BDP'];

    if(inArray(needle,haystack)){
        regatetd.style.display = '';
        regateth.style.display = '';
    }else{
        regatetd.style.display = 'none';
        regateth.style.display = 'none';
    }
}

function inArray(needle, haystack) {
    var length = haystack.length;
    for(var i = 0; i < length; i++) {
        if(haystack[i] == needle) return true;
    }
    return false;
}