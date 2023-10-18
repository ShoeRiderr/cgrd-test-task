import { fetchAll } from "./api/post.js";
import { handleNodeGroup, changeAttributeValue } from "./utils/node.js";

let post = {};
const submitBtn = document.getElementById("submit-btn");
const formTitle = document.getElementById("form-title");
const closeEditBtn = document.getElementById("close-edit");
const editBtns = document.querySelectorAll("[data-post-id]");

// FormFields
const form = document.getElementById("form");
const title = document.getElementById("title");
const description = document.getElementById("description");

window.onload = () => {
  handleNodeGroup(editBtns, handleEditBtns);
};

// Handle edit post buttons
const handleEditBtns = async (event, node) => {
  const postId = node.getAttribute("data-post-id");

  await editPost(postId);
};

closeEditBtn.onclick = async (e) => {
  await closeEdit();
};

async function editPost(id) {
  post = await (await fetchAll(id)).json();

  await _handleEditResponse();
}

async function _handleEditResponse() {
  if (Object.keys(post).length > 0) {
    openEdit();
  } else {
    closeEdit();
  }
}

async function closeEdit() {
  submitBtn.value = "Create";
  formTitle.textContent = "Create News";
  closeEditBtn.classList.add("none");

  title.value = "";
  description.value = "";

  await changeAttributeValue("form", "action", `/post`);

  await handleForm(false);
}

async function openEdit() {
  submitBtn.value = "Save";
  formTitle.textContent = "Edit News";
  closeEditBtn.classList.remove("none");

  title.value = post.title;
  description.value = post.description;

  await changeAttributeValue("form", "action", `/post/${post.id}`);

  await handleForm(true);
}

async function handleForm(isEdit = false) {
  if (isEdit) {
    _handleMethodInput(isEdit);

    return;
  }

  _handleMethodInput();
}

async function _handleMethodInput(isEdit = false) {
  const formMethodInput = form.querySelector('[name="_method"]');

  if (!isEdit) {
    formMethodInput.remove();

    return;
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
