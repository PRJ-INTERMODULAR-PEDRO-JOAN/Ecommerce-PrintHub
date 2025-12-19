// src/js/api.js

const SERVER_PRODUCTS = "http://localhost:3000"; // products.json
const SERVER_USERS    = "http://localhost:3001"; // users.json
const SERVER_COMMENTS = "http://localhost:3002"; // comments.json
const SERVER_LIKES    = "http://localhost:3003"; // likes.json

// --- PRODUCTOS E IMPRESORAS ---

export async function getDBProducts() {
    try {
        const response = await fetch(SERVER_PRODUCTS + "/productes");
        if (!response.ok) throw `Error ${response.status} de la BBDD`;
        return await response.json();
    } catch (error) {
        throw new Error(error);
    }
}

export async function getDBPrinters() {
    try {
        const response = await fetch(SERVER_PRODUCTS + "/impresoras");
        if (!response.ok) throw `Error ${response.status} de la BBDD`;
        return await response.json();
    } catch (error) {
        throw new Error(error);
    }
}

export async function getDBItemDetail(tipo, id) {
    try {
        // json-server devuelve un array cuando filtras por ?id=
        const response = await fetch(`${SERVER_PRODUCTS}/${tipo}?id=${id}`);
        if (!response.ok) throw `Error ${response.status}`;
        const data = await response.json();
        return data[0]; // Devolvemos el objeto limpio
    } catch (error) {
        throw new Error(error);
    }
}

// --- COMENTARIOS ---

export async function getDBComments(productId) {
    const response = await fetch(`${SERVER_COMMENTS}/comments?product_id=${productId}`);
    if (!response.ok) throw "Error cargando comentarios";
    return await response.json();
}

export async function addDBComment(comment) {
    const response = await fetch(`${SERVER_COMMENTS}/comments`, {
        method: "POST",
        body: JSON.stringify(comment),
        headers: { "Content-Type": "application/json" },
    });
    return await response.json();
}

export async function updateDBComment(id, data) {
    const response = await fetch(`${SERVER_COMMENTS}/comments/${id}`, {
        method: "PATCH",
        body: JSON.stringify(data),
        headers: { "Content-Type": "application/json" },
    });
    return await response.json();
}

export async function removeDBComment(id) {
    await fetch(`${SERVER_COMMENTS}/comments/${id}`, { method: "DELETE" });
    return true;
}

// --- LIKES ---

export async function getDBLikes(productId) {
    const response = await fetch(`${SERVER_LIKES}/likes?product_id=${productId}`);
    return await response.json();
}

export async function checkDBUserLike(productId, userId) {
    const response = await fetch(`${SERVER_LIKES}/likes?product_id=${productId}&user_id=${userId}`);
    const data = await response.json();
    return data[0] || null;
}

export async function addDBLike(data) {
    await fetch(`${SERVER_LIKES}/likes`, {
        method: "POST",
        body: JSON.stringify(data),
        headers: { "Content-Type": "application/json" }
    });
}

export async function removeDBLike(id) {
    await fetch(`${SERVER_LIKES}/likes/${id}`, { method: "DELETE" });
}

// --- USUARIOS (Para obtener nombres) ---
export async function getDBUser(id) {
    try {
        const response = await fetch(`${SERVER_USERS}/usuaris/${id}`);
        if (!response.ok) return { nom_usuari: "Anónimo" };
        return await response.json();
    } catch (e) { return { nom_usuari: "Anónimo" }; }
}
