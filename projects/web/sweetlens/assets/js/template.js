document.getElementById("photoInput").onchange = function(e) {
  document.getElementById("userPhoto").src =
    URL.createObjectURL(e.target.files[0]);
};
