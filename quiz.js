// Load quizzes from localStorage
let quizzes = JSON.parse(localStorage.getItem("quizzes")) || {};

document.getElementById("quizForm").addEventListener("submit", function (e) {
  e.preventDefault();

  const title = document.getElementById("quizTitle").value.trim();
  const question = document.getElementById("quizQuestion").value.trim();
  const options = {
    A: document.getElementById("optionA").value.trim(),
    B: document.getElementById("optionB").value.trim(),
    C: document.getElementById("optionC").value.trim(),
    D: document.getElementById("optionD").value.trim(),
  };
  const correctAnswer = document.getElementById("correctAnswer").value.trim().toUpperCase();

  if (!quizzes[title]) quizzes[title] = [];
  quizzes[title].push({ question, options, correctAnswer });

  renderQuizList();
  this.reset();
});

function renderQuizList() {
  const quizList = document.getElementById("quizList");
  quizList.innerHTML = "";

  // Save quizzes to localStorage
  localStorage.setItem("quizzes", JSON.stringify(quizzes));

  Object.keys(quizzes).forEach((title) => {
    const quizDiv = document.createElement("div");
    quizDiv.className = "p-4 border rounded bg-white shadow";

    const titleEl = document.createElement("h3");
    titleEl.textContent = title;
    titleEl.className = "text-lg font-semibold mb-2";

    const startBtn = document.createElement("button");
    startBtn.textContent = "Attempt Quiz";
    startBtn.className = "bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded";
    startBtn.onclick = () => renderQuizAttempt(title);

    quizDiv.appendChild(titleEl);
    quizDiv.appendChild(startBtn);
    quizList.appendChild(quizDiv);
  });
}

function renderQuizAttempt(title) {
  const quizAttemptSection = document.getElementById("quizAttemptSection");
  quizAttemptSection.innerHTML = "";

  const quiz = quizzes[title];
  if (!quiz) return;

  const form = document.createElement("form");
  form.className = "space-y-6";

  quiz.forEach((q, index) => {
    const qDiv = document.createElement("div");
    qDiv.className = "bg-white p-4 border rounded shadow";

    const qTitle = document.createElement("p");
    qTitle.textContent = `${index + 1}. ${q.question}`;
    qTitle.className = "font-medium mb-2";

    qDiv.appendChild(qTitle);

    Object.entries(q.options).forEach(([key, val]) => {
      const label = document.createElement("label");
      label.className = "block mb-1";

      const radio = document.createElement("input");
      radio.type = "radio";
      radio.name = `q${index}`;
      radio.value = key;
      radio.required = true;
      radio.className = "mr-2";

      label.appendChild(radio);
      label.appendChild(document.createTextNode(`${key}. ${val}`));
      qDiv.appendChild(label);
    });

    form.appendChild(qDiv);
  });

  const submitBtn = document.createElement("button");
  submitBtn.type = "submit";
  submitBtn.textContent = "Submit Quiz";
  submitBtn.className = "bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded";

  form.appendChild(submitBtn);

  form.addEventListener("submit", function (e) {
    e.preventDefault();

    let score = 0;
    quiz.forEach((q, index) => {
      const selected = form.querySelector(`input[name=q${index}]:checked`);
      if (selected && selected.value === q.correctAnswer) {
        score++;
      }
    });

    alert(`You scored ${score} out of ${quiz.length}`);
    quizAttemptSection.innerHTML = "";
  });

  quizAttemptSection.appendChild(form);
}

// Initial load
renderQuizList();
