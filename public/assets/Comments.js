// import axios from 'axios';

console.log("something");

function getComments(post_id) {
  axios
    .get("../get_comments.php", {
      params: {
        post_id: post_id,
      },
    })
    .then(function (response) {
      console.log(response);
      const data = response.data;
      renderComments(data);
    })
    .catch(function (error) {
      console.log(error);
    })
    .catch(function () {
      // always executed
    });
}

function renderComments(data) {
  const commentContent = document.getElementById("commentContent");

  // Clear existing content
  commentContent.innerHTML = "";

  // Create HTML elements to display the data
  data.forEach(function (comment) {
    console.log(comment);
    const messageItem = document.createElement("div");
    messageItem.className = "message-item";

    const messageTitle = document.createElement("div");
    messageTitle.className = "message-title";

    const readerName = document.createElement("div");
    readerName.innerText = comment.reader_name;

    const spacer = document.createElement("div");
    spacer.className = "spacer";

    messageTitle.appendChild(readerName);
    messageTitle.appendChild(spacer);
    messageItem.appendChild(messageTitle);

    const lines = comment.comment.split("\n");
    lines.forEach(function (line) {
      const messageLine = document.createElement("p");
      messageLine.className = "message-line";
      messageLine.innerText = line;
      messageItem.appendChild(messageLine);
    });

    commentContent.appendChild(messageItem);
  });
}
