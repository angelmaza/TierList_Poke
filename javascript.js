//con lso datos del input, llama a la api para cargar o crear una lista nueva
function carga_lista() {

    let endpoint = "json_tier.php?actionapi=cargarinicio"; 
    let tier_id = document.getElementById("tier_id").value;
    let url = endpoint+"&tier_id="+tier_id;

    fetch(url)
    .then(response => {
        if (response.ok)
            return response.text();
        throw new Error(response.status + ": " + response.statusText);
    })
    .then(data => {
        let decoded = JSON.parse(data);
        console.log(decoded);

        //Si habia pokemons antes, los borramos quitando el html
        for(let i=1; i<=6; ++i) {
            document.getElementById("level"+i).innerHTML = "<strong>Nivel "+i+"</strong>";
        }
        
        decoded.forEach(pokemon => {
            let div = document.createElement("div");
            div.classList.add("divpokemon");
            div.id = pokemon.pok_id;  // pok_id como id
            div.setAttribute("draggable", true);
            div.setAttribute("ondragstart", "arrastrar(event)");

            let img = document.createElement("img");  //creamos img
            img.src = "images_pokemon/" + pokemon.pngname  + ".png"; 
            img.alt = pokemon.nombre;

            let texto = document.createElement("span"); //creamos txtnombre
            texto.textContent = pokemon.nombre;

            div.appendChild(img);
            div.appendChild(texto);

            let contenedor = document.getElementById("level"+pokemon.level);
            contenedor.appendChild(div);
        })
        document.getElementById("tier_list").style.visibility = "visible";
    })
    .catch(err => {
        document.getElementById("tier_list").style.visibility = "hidden";
        document.getElementById("error").innerText = err;
    });
}




///Funcion que recoge los datos del elemento que arrastras.
function arrastrar(event) {
    event.dataTransfer.setData("id", event.currentTarget.id); //con current target porque sino da problemas los hijos
}

//Funcion que al soltar un elemento(pokemon), con los datos de dicho elemento mas los datos de level, cargara los datos en la api, y a su vez lo mostrara en pantalla
function soltar(event, level) {
    event.preventDefault();

    let endpoint = "json_tier.php?actionapi=updatecarga"; 
    let ch_id = event.dataTransfer.getData("id");
    let tier_id = document.getElementById("tier_id").value;
    let lvlpok = level.replace(/\D/g, ''); //quito las letras lvl

    let url = endpoint+"&idpok="+ch_id+"&tier_id="+tier_id+"&lvlpok="+lvlpok;
    console.log(url);
    fetch(url, {method: "GET"})
    .then(response => {

        console.log(response);
        if (response.ok)
            return response.text();
        throw new Error(response.status + ": " + response.statusText);
    })
    .then(data => {
        try {
            let decoded = JSON.parse(data);

            decoded.forEach(pokemon => {
                let pokeElement = document.getElementById(pokemon.pok_id);
                let leveldiv = document.getElementById("level"+pokemon.level);

                // Mueve el Pokémon al nivel correspondiente
                if (pokeElement && leveldiv) {
                    leveldiv.appendChild(pokeElement);
                } else {
                    console.error("No se encontró el contenedor para:", pokemon);
                }
            });

        } catch (e) {
            console.error("Error al parsear JSON:", e);
            document.getElementById("error").innerText = "Error al parsear JSON: " + e.message;
        }
})
    .catch(err => {
        document.getElementById("error").innerText = err;
    });
}

window.onload = function() {
    carga_lista();
}


// function carga_lista() {
//     // http://localhost/angel/tema6_async/proyecto/json_tier.php?&tier_id=7&idpok=39&lvlpok=2

//     let endpoint = "json_tier.php?"; 
//     // let tier_name = document.getElementById("tier_name").value;
//     let tier_id = document.getElementById("tier_id").value;
//     let url = endpoint+"&tier_id="+tier_id;
//     console.log(url);

//     fetch(url)
//     .then(response => {
//         if (response.ok)
//             return response.text();
//         throw new Error(response.status + ": " + response.statusText);
//     })
//     .then(data => {
//         let decoded = JSON.parse(data);
//         console.log(decoded);

//         //Si habia pokemons antes, los borramos quitando el html
//         for(let i=1; i<=6; ++i) {
//             document.getElementById("level"+i).innerHTML = "<strong>Nivel "+i+"</strong>";
//         }
        
//         decoded.creatures.forEach(ch => {
//             let div = document.createElement("div");
//             div.classList.add("character");
//             div.id = ch.id;
//             div.setAttribute("draggable", true);
//             div.setAttribute("ondragstart", "arrastrar(event)");

//             let img = document.createElement("img");
//             img.src = ch.pic; 
//             img.alt = ch.name;

//             let texto = document.createElement("span");
//             texto.textContent = ch.name;

//             div.appendChild(img);
//             div.appendChild(texto);

//             let contenedor = document.getElementById("level"+ch.level);
//             contenedor.appendChild(div);
//         })
//         document.getElementById("tier_id").value = decoded.id;
//         document.getElementById("tier_list").style.visibility = "visible";
//     })
//     .catch(err => {
//         document.getElementById("tier_list").style.visibility = "hidden";
//         document.getElementById("error").innerText = err;
//     });
// }