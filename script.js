const API_URL = "http://localhost/API-PROVA/user.php";

async function load(pesquisa = "") {
    let url = API_URL;
    if (pesquisa) {
        url += "?pesquisa=" + encodeURIComponent(pesquisa);
    }

    const resposta = await fetch(url);
    const dados = await resposta.json();

    const tbody = document.querySelector("#table tbody");
    tbody.innerHTML = "";

    dados.forEach(usuario => {
        const tr = document.createElement("tr");
        tr.innerHTML = `
      <td>${usuario.id}</td>
      <td>${usuario.login}</td>
      <td>${usuario.name}</td>
      <td>${usuario.email}</td>
      <td>${usuario.password}</td>
      <td>${usuario.active == 1 ? "Yes" : "No"}</td>
      <td>
        <button onclick='edit(${JSON.stringify(usuario)})' class="btn-edit"><i class="fa-solid fa-pen"></i></button>
        <button onclick='deletar(${usuario.id})' class="btn-trash"><i class="fa-solid fa-trash"></i></button>
      </td>
    `;
        tbody.appendChild(tr);
    });
}

function loadsearch() {
    const valor = document.getElementById("pesquisa").value;
    load(valor);
}

async function salvar() {
    const id = document.getElementById("id").value;
    const payload = {
        id: id,
        login: document.getElementById("login").value,
        name: document.getElementById("nome").value,
        email: document.getElementById("email").value,
        password: document.getElementById("password").value,
        active: document.getElementById("active").value
    };

    if (id) {
        await fetch(API_URL, {
            method: "PUT",
            body: JSON.stringify(payload)
        });
    } else {
        await fetch(API_URL, {
            method: "POST",
            body: JSON.stringify(payload)
        });
    }
    limpar();
    load();
}

function edit(usuario) {
    document.getElementById("id").value = usuario.id;
    document.getElementById("login").value = usuario.login;
    document.getElementById("nome").value = usuario.name;
    document.getElementById("email").value = usuario.email;
    document.getElementById("password").value = usuario.password;
    document.getElementById("active").value = usuario.active;
}

async function deletar(id) {
    if (confirm("Are you sure you want to delete this user?")) {
        await fetch(API_URL + "?id=" + id, { method: "DELETE" });
        load();
    }
}

function limpar() {
    document.getElementById("id").value = "";
    document.getElementById("login").value = "";
    document.getElementById("nome").value = "";
    document.getElementById("email").value = "";
    document.getElementById("password").value = "";
    document.getElementById("active").value = "";
}
