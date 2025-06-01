const links = document.querySelectorAll('a.NoFinish');

links.forEach(link => {
  link.addEventListener('click', function(event) {
    event.preventDefault();
    alert("Chức năng đang trong quá trình xây dựng");
  });
});
