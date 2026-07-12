/* Buscador */
const buscador = document.getElementById("buscador");

buscador.addEventListener("input", () => {

    const texto = normalizar(buscador.value);

    document.querySelectorAll(".preguntasGrid.fila").forEach(fila => {

        const pregunta = normalizar(fila.children[1].textContent);

        fila.style.display = pregunta.includes(texto)
            ? "grid"
            : "none";

    });

});

function normalizar(texto) {
    return texto
        .toLowerCase()
        .normalize("NFD")
        .replace(/[\u0300-\u036f]/g, "")
        .replace(/[^\p{L}\p{N}\s]/gu, "");
}
/* Modal Modificar categoria */
const modalCategoria = document.getElementById("modalCategoria");

const inputId = document.getElementById("categoriaId");
const inputNombre = document.getElementById("categoriaNombre");
const inputColor = document.getElementById("categoriaColor");

const preview = document.getElementById("previewColor");

document.querySelectorAll(".btnModificarCategoria").forEach(boton=>{

    boton.addEventListener("click",()=>{

        inputId.value = boton.dataset.id;

        inputNombre.value = boton.dataset.nombre;

        inputColor.value = boton.dataset.color;

        preview.style.backgroundColor = boton.dataset.color;

        modalCategoria.classList.add("active");

    });

});

inputColor.addEventListener("input",()=>{

    preview.style.backgroundColor = inputColor.value;

});

function cerrarCategoria(){

    modalCategoria.classList.remove("active");

}

document.getElementById("cerrarCategoria")
    .addEventListener("click",cerrarCategoria);

document.getElementById("cancelarCategoria")
    .addEventListener("click",cerrarCategoria);

modalCategoria.addEventListener("click",e=>{

    if(e.target===modalCategoria){
        cerrarCategoria();
    }

});
/* Modal Crear pregunta */
const ModalBtn = document.getElementById("modalBtn");
const crearModal = document.getElementById("crearModal");
const closeModal = document.getElementById("closeModal");
const cancelModal = document.getElementById("cancelModal");

if (ModalBtn && crearModal) {
    ModalBtn.addEventListener("click", () => {
        crearModal.classList.add("active");
    });
}

function cerrarModal(){
    if (crearModal) {
        crearModal.classList.remove("active");
    }
}

if (closeModal) closeModal.addEventListener("click", cerrarModal);
if (cancelModal) cancelModal.addEventListener("click", cerrarModal);

if (crearModal) {
    crearModal.addEventListener("click",(e)=>{
        if(e.target === crearModal){
            cerrarModal();
        }
    });
}

document.addEventListener("keydown",(e)=>{
    if(e.key === "Escape"){
        cerrarModal();
    }
});

const select = document.getElementById("categoria");
const nueva = document.getElementById("nuevaCategoria");

select.addEventListener("change", () => {

    if(select.value === "nueva"){
        nueva.style.display = "block";
    }else{
        nueva.style.display = "none";
    }

});
/* Boton preguntas y categorias */
const btnPreguntas = document.getElementById("btnPreguntas");
const btnCategorias = document.getElementById("btnCategorias");

const listaPreguntas = document.getElementById("listaPreguntas");
const listaCategorias = document.getElementById("listaCategorias");

btnPreguntas.addEventListener("click", () => {
    listaPreguntas.hidden = false;
    listaCategorias.hidden = true;

    btnPreguntas.classList.add("active");
    btnCategorias.classList.remove("active");
});

btnCategorias.addEventListener("click", () => {
    listaPreguntas.hidden = true;
    listaCategorias.hidden = false;

    btnCategorias.classList.add("active");
    btnPreguntas.classList.remove("active");
});