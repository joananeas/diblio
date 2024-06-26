
// document.getElementById("buscador").addEventListener("focusout", function() {
//     document.getElementById("buscadorLlibres").style.display = "none";
// });
document.getElementById("inputCercaLlibres").addEventListener("focus", function () {
    document.getElementById("buscadorLlibres").style.display = "block";
});

document.getElementById("inputCercaLlibres").addEventListener("input", function () {
    let formData = new FormData();
    formData.append('pttn', 'cercaLlibresLite');
    formData.append('llibre', this.value);
    if (this.value !== "") {
        fetch("./mantenimiento/api.php", {
            method: "POST",
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                // console.log("[RESPONSE: Cerca] ", data);
                if (data.response === "OK") {
                    let response = data.llibres;
                    let desplegable = document.getElementById("buscadorLlibres");
                    desplegable.innerHTML = "";
                    for (let i = 0; i < response.length; i++) {
                        let libro = response[i];

                        let estadoLibro = document.createElement("span");
                        let tituloLibro = document.createElement("li");

                        estadoLibro.className = "estadoLlibro";
                        if (libro.estadoActual === "Disponible") estadoLibro.style.color = "green", estadoLibro.innerHTML = "Disponible";
                        else estadoLibro.style.color = "red", estadoLibro.innerHTML = "No disponible";

                        tituloLibro.className = "llibre";
                        tituloLibro.appendChild(estadoLibro);
                        tituloLibro.appendChild(document.createTextNode(libro.nom));

                        // Agregar botón al final del elemento tituloLibro
                        let boton = document.createElement("button");
                        boton.innerHTML = "Veure més";
                        boton.className = "botonUniversal";
                        boton.style.margin = "0px";
                        boton.style.float = "right";
                        tituloLibro.appendChild(boton);

                        desplegable.appendChild(tituloLibro);
                        boton.addEventListener("click", function () {
                            console.log("Reservar: ", libro.nom);
                            window.location.href = `./libro.php?libro=${libro.id}`;
                        });

                        if (i === response.length - 1) {
                            tituloLibro.style.borderBottom = "none";
                        }
                    }
                }
            })
            .catch(error => {
                console.log("[ERROR (API_Request)] ", error);
            });
    }
});

const cBack = document.getElementById("c-anterior");
const cNext = document.getElementById("c-siguiente");
const cPhoto = document.getElementById("c-foto");
let tmpData;

const getFotos = () => {
    let formData = new FormData();
    formData.append('pttn', 'getFotos');
    fetch('./mantenimiento/api.php', {
        method: "POST",
        body: formData
    }).then(response => response.json())
        .then(data => {
            console.log("[RESPONSE: Cerca] ", data);
            tmpData = data.num_libros
            const puntosCarroussel = document.getElementById("puntos-carroussel");
            for (let i = 0; i < data.num_libros; i++) {
                let li = document.createElement("li");
                li.id = "c-dot-" + i;
                if (i === 0) li.className = "activo";
                puntosCarroussel.appendChild(li);
            }
            console.log(data)
        })
        .catch(error => {
            console.log("[ERROR (API_Request)] ", error);
        });
};

const updateActiveDot = (newActiveIndex) => {
    const dots = document.querySelectorAll("#puntos-carroussel li");
    dots.forEach(dot => dot.classList.remove("activo"));
    const newActiveDot = document.getElementById("c-dot-" + newActiveIndex);
    if (newActiveDot) newActiveDot.classList.add("activo");
};

const r = () => {
    let src = cPhoto.src;
    let partes = src.split('-');
    let srcNumericoConExtension = partes.slice(1).join('-');
    let partesPuntos = srcNumericoConExtension.split('.');
    let srcNumericoSinExtension = partesPuntos.slice(0, -1).join('.');
    srcNumericoSinExtension = parseInt(srcNumericoSinExtension);
    if ((srcNumericoSinExtension < tmpData + 1) && (srcNumericoSinExtension > 1)) srcNumericoSinExtension--;
    else srcNumericoSinExtension = tmpData;

    console.log("num: ", tmpData, " actual: ", srcNumericoSinExtension);
    let newActiveIndex = srcNumericoSinExtension - 1;
    updateActiveDot(newActiveIndex);
    //console.log("nombre", partes,"Ext", partesPuntos[1], "Num: ", srcNumericoSinExtension);
    console.log("SRC: ", partes[0] + "-" + srcNumericoSinExtension + "." + partesPuntos[1]);
    cPhoto.src = partes[0] + "-" + srcNumericoSinExtension + "." + partesPuntos[1];
}

cBack.addEventListener("click", function () {
    r()
});

const l = () => {
    const src = cPhoto.src;
    const partes = src.split('-');
    const srcNumericoConExtension = partes.slice(1).join('-');
    const partesPuntos = srcNumericoConExtension.split('.');
    let srcNumericoSinExtension = partesPuntos.slice(0, -1).join('.');
    srcNumericoSinExtension = parseInt(srcNumericoSinExtension);
    console.log("num: ", tmpData, " actual: ", srcNumericoSinExtension);
    if (tmpData > srcNumericoSinExtension) srcNumericoSinExtension++;
    else srcNumericoSinExtension = 1;

    let newActiveIndex = srcNumericoSinExtension - 1;
    updateActiveDot(newActiveIndex);
    //console.log("nombre", partes,"Ext", partesPuntos[1], "Num: ", srcNumericoSinExtension);
    console.log("SRC: ", partes[0] + "-" + srcNumericoSinExtension + "." + partesPuntos[1]);
    cPhoto.src = partes[0] + "-" + srcNumericoSinExtension + "." + partesPuntos[1];
}

cNext.addEventListener("click", function () {
    l()
});


// Controla el swipe para moviles

let touchstartX = 0
let touchendX = 0

function checkDirection() {
    if (touchendX < touchstartX) l()
    if (touchendX > touchstartX) r()
}

cPhoto.addEventListener('touchstart', e => {
    touchstartX = e.changedTouches[0].screenX
})

cPhoto.addEventListener('touchend', e => {
    touchendX = e.changedTouches[0].screenX
    checkDirection()
})


// Categories
const generateRandomSpans = (categoriesList, categoriesDiv) => {
    const categoriesLength = categoriesList.length;
    for (let i = 0; i < categoriesLength; i++) {
        const randomIndex = Math.floor(Math.random() * categoriesList.length);
        const randomCategory = categoriesList[randomIndex];

        const span = document.createElement("span");
        span.textContent = randomCategory;
        span.classList.add("categoria");
        span.classList.add("botonUniversal");

        span.addEventListener("click", () => {
            viewPopUp('popupCategories', 'closeCategories', 'close');
            document.getElementById("inputCercaLlibres").value = "c:" + randomCategory; // Indica que busca una categoria
            window.location.href = "#inputCercaLlibres";
            document.getElementById("inputCercaLlibres").focus();
            document.getElementById("inputCercaLlibres").dispatchEvent(new Event('input'));
        });
        categoriesDiv.appendChild(span);
    }
};

let categoriesList = [];
const fillCategories = async () => {
    let response = await getCategories();
    const categoriesDiv = document.getElementById("categoriesContainer");
    const categoriesDivPopUp = document.getElementById("categoriesListPopup");

    if (response) {
        const categories = response.message;

        for (let i = 0; i < categories.length; i++) {
            let category = categories[i].MATERIA;

            if (category === '') continue;
            categoriesList.push(category);
        }
    }

    generateRandomSpans(categoriesList, categoriesDiv);
    generateRandomSpans(categoriesList, categoriesDivPopUp);
};

const cercarCategories = () => {
    const input = document.getElementById("cercadorCategories");
    const value = input.value.toLowerCase();
    const spans = document.querySelectorAll(".categoria");

    spans.forEach(span => {
        const categoria = span.textContent.toLowerCase();
        if (categoria.includes(value)) {
            span.style.display = "block";
        } else {
            span.style.display = "none";
        }
    });
};

setInterval(l, 5000);
document.addEventListener("DOMContentLoaded", getFotos());
document.addEventListener("DOMContentLoaded", fillCategories());
document.getElementById("veurePopUpCategories").addEventListener("click", () => viewPopUp('popupCategories', 'closeCategories'), console.log("PopUp Cat"));
document.getElementById("cercadorCategories").addEventListener("input", cercarCategories);