// Add onClick event to Node list elements
export const addOnClickNodeListEl = (nodes, callback) => {
  let i;

  for (i = 0; i < nodes.length; i += 1) {
    ((i) => {
      let singleNode = nodes[i];

      singleNode.onclick = (event) => {
        callback(event, singleNode);
      };
    })(i);
  }
};

export const changeAttributeValue = async (id, attrName, value) => {
  document.getElementById(id).setAttribute(attrName, value);
};
