(() => {
  const board = document.getElementById("board"),
        btn = document.getElementById("startBtn"),
        mEl = document.getElementById("moves"),
        pEl = document.getElementById("pairs"),
        COLORS = ["#ef4444","#3b82f6","#22c55e","#a855f7","#f97316","#14b8a6","#eab308","#ec4899"];

  let deck, first, lock, moves, pairs;

  // Fisher-Yates correct
  const shuffle = a => {
    for (let i = a.length - 1; i > 0; i--) {
      const j = Math.floor(Math.random() * (i + 1));
      [a[i], a[j]] = [a[j], a[i]];
    }
    return a;
  };

  function reset() {
    deck = shuffle([...COLORS, ...COLORS]);
    board.innerHTML = deck.map((c,i)=>`<div class="card" data-i="${i}"></div>`).join("");
    moves = pairs = 0; first = null; lock = false;
    mEl.textContent = pEl.textContent = 0;
  }

  board.onclick = e => {
    const c = e.target.closest(".card");
    if (!c || lock || c.classList.contains("revealed") || c.classList.contains("matched")) return;

    const i = c.dataset.i;
    c.style.background = deck[i];
    c.classList.add("revealed");

    if (!first) return first = c;

    moves++; mEl.textContent = moves;

    if (deck[first.dataset.i] === deck[i]) {
      first.classList.add("matched");
      c.classList.add("matched");
      first = null;
      pEl.textContent = ++pairs;
      if (pairs === COLORS.length) setTimeout(()=>alert(`Bravo ! Terminé en ${moves} coups.`),150);
    } else {
      lock = true;
      setTimeout(()=> {
        [first,c].forEach(el=>{
          el.classList.remove("revealed");
          el.style.background = "var(--card-back)";
        });
        first = null; lock = false;
      },700);
    }
  };

  btn.onclick = reset;
  document.addEventListener("DOMContentLoaded", reset);
})();
