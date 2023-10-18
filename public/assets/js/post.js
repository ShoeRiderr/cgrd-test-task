import { fetchAll } from "./api/post.js";
import { addOnClickNodeListEl, changeAttributeValue } from "./utils/node.js";

let post = {};

// Node list for post edit
const editPostBtnList = document.querySelectorAll("[data-post-id]");

// Nodes to transform when switching form type
const submitBtn = document.getElementById("submit-btn");
const formTitle = document.getElementById("form-title");
const closeEditBtn = document.getElementById("close-edit");

// FormFields
const form = document.getElementById("form");
const title = document.getElementById("title");
const description = document.getElementById("description");

// Wait for loading all components to the page
window.onload = () => {
  // Add onclick event listener for Nodes from editPostBtnList Node list 
  addOnClickNodeListEl(editPostBtnList, handleEditBtns);
};

// Handle edit post buttons
const handleEditBtns = async (event, node) => {
  const postId = node.getAttribute("data-post-id");

  await editPost(postId);
};

// Listener for close button
closeEditBtn.onclick = async (e) => {
  await _closeEditForm();
};

// Method for fetching post and switch to edit form
async function editPost(id) {
  try {
    post = await (await fetchAll(id)).json();

    await _openEditForm();
  } catch (error) {
    console.log(error);
  }
}

async function _closeEditForm() {
  submitBtn.value = "Create";
  formTitle.textContent = "Create News";
  closeEditBtn.classList.add("none");

  title.value = "";
  description.value = "";

  await changeAttributeValue("form", "action", `/post`);

  await _handleForm(false);
}

async function _openEditForm() {
  submitBtn.value = "Save";
  formTitle.textContent = "Edit News";
  closeEditBtn.classList.remove("none");

  title.value = post.title;
  description.value = post.description;

  await changeAttributeValue("form", "action", `/post/${post.id}`);

  await _handleForm(true);
}

async function _handleForm(isEdit = false) {
  const formMethodInput = form.querySelector('[name="_method"]');

  if (!isEdit) {
    formMethodInput.remove();
  }

  if (!formMethodInput) {
    const input = Object.assign(document.createElement("input"), {
      type: "hidden",
      name: "_method",
      value: "PUT",
    });

    form.appendChild(input);
  }
}
