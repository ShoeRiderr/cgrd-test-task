import { request } from "./config.js";

export async function fetchAll(id) {
  return await request(`post/${id}`, "GET");
}
