var loadFile = function(event) {
    var file = document.getElementById('outputfile');
    file.src = URL.createObjectURL(event.target.files[0]);
};

