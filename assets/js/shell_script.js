const uploadInput = document.querySelector("#naxx");
uploadInput.addEventListener("change", () => {
  const uploadForm = document.querySelector("#upload");
  uploadForm.submit();
});


const mode = localStorage.getItem("mode") || "light";

const modeToggle = document.querySelector("#mode-toggle");
const modeToggleLabel = document.querySelector("[for=mode-toggle] span");
const body = document.body;

if (mode === "dark") {
  body.classList.add("dark");
  modeToggle.checked = true;
  modeToggleLabel.innerText = "Light Mode";
} else {
  body.classList.remove("dark");
  modeToggle.checked = false;
  modeToggleLabel.innerText = "Dark Mode";
}

modeToggle.addEventListener("change", () => {
  if (modeToggle.checked) {
    localStorage.setItem("mode", "dark");
    body.classList.add("dark");
    modeToggleLabel.innerText = "Light Mode";
  } else {
    localStorage.setItem("mode", "light");
    body.classList.remove("dark");
    modeToggleLabel.innerText = "Dark Mode";
  }
});

