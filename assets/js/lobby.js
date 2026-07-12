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
const inputColor = document.getElementById("categoriaColor");
const preview = document.getElementById("previewColor");
inputColor.addEventListener("input",()=>{

    preview.style.backgroundColor = inputColor.value;

});