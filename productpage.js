function getQueryParam(param) {
    const urlParams = new URLSearchParams(window.location.search);
    return urlParams.get(param);
}

function changeImage(thumbnail) {
    const mainImage = document.getElementById('productImage');
    mainImage.src = thumbnail.src;
}

document.getElementById('productName').innerText = getQueryParam('name');
document.getElementById('productPrice').innerText = getQueryParam('price');
document.getElementById('productImage').src = getQueryParam('image');
document.getElementById('thumbnail1').src = getQueryParam('image1');
document.getElementById('thumbnail2').src = getQueryParam('image2');
document.getElementById('thumbnail3').src = getQueryParam('image3');
document.getElementById('productBrand').innerText = getQueryParam('brand');
document.getElementById('productDescription').innerText = getQueryParam('description');
document.getElementById('addToCart').innerHTML=getQueryParam('addToCart')




