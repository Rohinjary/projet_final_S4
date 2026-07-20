(() => {
  "use strict";

  const STORAGE = {
    phone: "mobipay_phone",
    clients: "mobipay_clients",
    transactions: "mobipay_transactions",
    prefixes: "mobipay_prefixes",
    scales: "mobipay_scales"
  };

  const TODAY = new Date();
  const toISODate = (date) => date.toISOString().slice(0, 10);
  const shiftedISO = (days) => {
    const d = new Date(TODAY);
    d.setDate(d.getDate() - days);
    return toISODate(d);
  };

  const DEFAULT_PREFIXES = [
    { id: 1, prefix: "033", label: "MobiPay A", active: true },
    { id: 2, prefix: "034", label: "MobiPay B", active: true },
    { id: 3, prefix: "037", label: "MobiPay C", active: true },
    { id: 4, prefix: "038", label: "MobiPay D", active: false }
  ];

  const DEFAULT_SCALES = {
    depot: [{ id: 1, min: 100, max: 2000000, fee: 0 }],
    retrait: [
      { id: 1, min: 100, max: 1000, fee: 50 },
      { id: 2, min: 1001, max: 5000, fee: 50 },
      { id: 3, min: 5001, max: 10000, fee: 100 },
      { id: 4, min: 10001, max: 25000, fee: 200 },
      { id: 5, min: 25001, max: 50000, fee: 400 },
      { id: 6, min: 50001, max: 100000, fee: 800 },
      { id: 7, min: 100001, max: 250000, fee: 1500 },
      { id: 8, min: 250001, max: 500000, fee: 1500 },
      { id: 9, min: 500001, max: 1000000, fee: 2500 },
      { id: 10, min: 1000001, max: 2000000, fee: 3000 }
    ],
    transfert: [
      { id: 1, min: 100, max: 1000, fee: 50 },
      { id: 2, min: 1001, max: 5000, fee: 50 },
      { id: 3, min: 5001, max: 10000, fee: 100 },
      { id: 4, min: 10001, max: 25000, fee: 200 },
      { id: 5, min: 25001, max: 50000, fee: 400 },
      { id: 6, min: 50001, max: 100000, fee: 800 },
      { id: 7, min: 100001, max: 250000, fee: 1500 },
      { id: 8, min: 250001, max: 500000, fee: 1500 },
      { id: 9, min: 500001, max: 1000000, fee: 2500 },
      { id: 10, min: 1000001, max: 2000000, fee: 3000 }
    ]
  };

  const DEFAULT_CLIENTS = [
    { id: 1, phone: "0341234567", balance: 115000, operations: 12, lastActivity: shiftedISO(0), active: true },
    { id: 2, phone: "0339876543", balance: 230500, operations: 7, lastActivity: shiftedISO(0), active: true },
    { id: 3, phone: "0375511298", balance: 48000, operations: 3, lastActivity: shiftedISO(1), active: true },
    { id: 4, phone: "0347188201", balance: 0, operations: 1, lastActivity: shiftedISO(5), active: false },
    { id: 5, phone: "0382044177", balance: 750000, operations: 22, lastActivity: shiftedISO(0), active: true },
    { id: 6, phone: "0331422356", balance: 12500, operations: 4, lastActivity: shiftedISO(2), active: true },
    { id: 7, phone: "0348899123", balance: 320000, operations: 15, lastActivity: shiftedISO(3), active: true },
    { id: 8, phone: "0373366789", balance: 5000, operations: 2, lastActivity: shiftedISO(4), active: false }
  ];

  const DEFAULT_TRANSACTIONS = [
    { id: 1, ref: "DEP-204781", type: "depot", client: "0341234567", amount: 50000, fee: 0, direction: "+", date: shiftedISO(0), time: "09:15" },
    { id: 2, ref: "TRF-198432", type: "transfert", client: "0341234567", recipient: "0339876543", amount: 20000, fee: 200, direction: "-", date: shiftedISO(1), time: "16:42" },
    { id: 3, ref: "RET-187654", type: "retrait", client: "0341234567", amount: 15000, fee: 200, direction: "-", date: shiftedISO(2), time: "11:03" },
    { id: 4, ref: "DEP-176543", type: "depot", client: "0341234567", amount: 100000, fee: 0, direction: "+", date: shiftedISO(3), time: "08:55" },
    { id: 5, ref: "TRF-165432", type: "transfert", client: "0341234567", recipient: "0375511298", amount: 5000, fee: 50, direction: "-", date: shiftedISO(4), time: "14:20" },
    { id: 6, ref: "RET-154321", type: "retrait", client: "0341234567", amount: 30000, fee: 400, direction: "-", date: shiftedISO(5), time: "10:11" },
    { id: 7, ref: "DEP-143210", type: "depot", client: "0341234567", amount: 200000, fee: 0, direction: "+", date: shiftedISO(6), time: "09:00" },
    { id: 8, ref: "TRF-204739", type: "transfert", client: "0347188201", recipient: "0382044177", amount: 5000, fee: 50, direction: "-", date: shiftedISO(0), time: "09:05" },
    { id: 9, ref: "RET-204765", type: "retrait", client: "0339876543", amount: 50000, fee: 800, direction: "-", date: shiftedISO(0), time: "09:31" },
    { id: 10, ref: "DEP-204750", type: "depot", client: "0375511298", amount: 100000, fee: 0, direction: "+", date: shiftedISO(0), time: "09:18" },
    { id: 11, ref: "RET-204720", type: "retrait", client: "0382044177", amount: 25000, fee: 400, direction: "-", date: shiftedISO(0), time: "08:52" }
  ];

  const GAIN_DATA = [
    { month: "Janvier", withdrawal: 120500, transfer: 87000 },
    { month: "Février", withdrawal: 145200, transfer: 102000 },
    { month: "Mars", withdrawal: 98700, transfer: 76500 },
    { month: "Avril", withdrawal: 165000, transfer: 118000 },
    { month: "Mai", withdrawal: 134800, transfer: 95000 },
    { month: "Juin", withdrawal: 178000, transfer: 125000 },
    { month: "Juillet", withdrawal: 42800, transfer: 31200 }
  ];

  const TYPE_META = {
    depot: { label: "Dépôt", icon: "bi-arrow-down-circle", className: "deposit", badge: "badge-soft-success" },
    retrait: { label: "Retrait", icon: "bi-arrow-up-circle", className: "withdraw", badge: "badge-soft-warning" },
    transfert: { label: "Transfert", icon: "bi-arrow-left-right", className: "transfer", badge: "badge-soft-primary" }
  };

  function getJSON(key, fallback) {
    try {
      const value = localStorage.getItem(key);
      return value ? JSON.parse(value) : structuredClone(fallback);
    } catch (_) {
      return JSON.parse(JSON.stringify(fallback));
    }
  }

  function setJSON(key, value) { localStorage.setItem(key, JSON.stringify(value)); }
  function getClients() { return getJSON(STORAGE.clients, DEFAULT_CLIENTS); }
  function setClients(value) { setJSON(STORAGE.clients, value); }
  function getTransactions() { return getJSON(STORAGE.transactions, DEFAULT_TRANSACTIONS); }
  function setTransactions(value) { setJSON(STORAGE.transactions, value); }
  function getPrefixes() { return getJSON(STORAGE.prefixes, DEFAULT_PREFIXES); }
  function setPrefixes(value) { setJSON(STORAGE.prefixes, value); }
  function getScales() { return getJSON(STORAGE.scales, DEFAULT_SCALES); }
  function setScales(value) { setJSON(STORAGE.scales, value); }

  function normalizePhone(value) {
    let digits = String(value || "").replace(/\D/g, "");
    if (digits.startsWith("261")) digits = "0" + digits.slice(3);
    if (digits.length === 9 && !digits.startsWith("0")) digits = "0" + digits;
    return digits.slice(0, 10);
  }

  function formatPhone(value) {
    const p = normalizePhone(value);
    if (p.length !== 10) return p;
    return `${p.slice(0, 3)} ${p.slice(3, 5)} ${p.slice(5, 8)} ${p.slice(8, 10)}`;
  }

  function formatMoney(value) {
    return `${Number(value || 0).toLocaleString("fr-FR")} Ar`;
  }

  function prettyDate(iso) {
    const d = new Date(`${iso}T12:00:00`);
    return d.toLocaleDateString("fr-FR", { day: "2-digit", month: "short", year: "numeric" });
  }

  function currentPhone() {
    return normalizePhone(localStorage.getItem(STORAGE.phone) || "0341234567");
  }

  function activePrefixes() {
    return getPrefixes().filter((item) => item.active).map((item) => item.prefix);
  }

  function isValidPhone(value) {
    const phone = normalizePhone(value);
    return phone.length === 10 && activePrefixes().some((prefix) => phone.startsWith(prefix));
  }

  function ensureClient(phone) {
    const normalized = normalizePhone(phone);
    const clients = getClients();
    let client = clients.find((item) => item.phone === normalized);
    if (!client) {
      client = {
        id: Date.now(),
        phone: normalized,
        balance: 0,
        operations: 0,
        lastActivity: toISODate(new Date()),
        active: true
      };
      clients.push(client);
      setClients(clients);
    }
    return client;
  }

  function getClient(phone = currentPhone()) { return ensureClient(phone); }

  function updateClient(phone, changes) {
    const normalized = normalizePhone(phone);
    const clients = getClients();
    const index = clients.findIndex((item) => item.phone === normalized);
    if (index < 0) {
      ensureClient(normalized);
      return updateClient(normalized, changes);
    }
    clients[index] = { ...clients[index], ...changes };
    setClients(clients);
    return clients[index];
  }

  function getFee(type, amount) {
    const scales = getScales()[type] || [];
    const row = scales.find((item) => amount >= Number(item.min) && amount <= Number(item.max));
    return row ? Number(row.fee) : 0;
  }

  function makeRef(type) {
    const prefix = type === "depot" ? "DEP" : type === "retrait" ? "RET" : "TRF";
    return `${prefix}-${String(Date.now()).slice(-6)}`;
  }

  function addTransaction(transaction) {
    const now = new Date();
    const transactions = getTransactions();
    const item = {
      id: Date.now(),
      ref: makeRef(transaction.type),
      client: currentPhone(),
      direction: transaction.type === "depot" ? "+" : "-",
      date: toISODate(now),
      time: now.toLocaleTimeString("fr-FR", { hour: "2-digit", minute: "2-digit" }),
      fee: 0,
      ...transaction
    };
    transactions.unshift(item);
    setTransactions(transactions);
    return item;
  }

  function showToast(message, type = "success") {
    let toast = document.querySelector(".app-toast");
    if (!toast) {
      toast = document.createElement("div");
      toast.className = "app-toast";
      document.body.appendChild(toast);
    }
    toast.className = `app-toast ${type}`;
    toast.textContent = message;
    requestAnimationFrame(() => toast.classList.add("show"));
    clearTimeout(showToast.timer);
    showToast.timer = setTimeout(() => toast.classList.remove("show"), 2600);
  }

  function updateSharedClientUI() {
    const phone = currentPhone();
    document.querySelectorAll("[data-current-phone]").forEach((el) => { el.textContent = formatPhone(phone); });
    document.querySelectorAll("[data-current-balance]").forEach((el) => { el.textContent = formatMoney(getClient(phone).balance); });
  }

  function operationItemHTML(tx) {
    const meta = TYPE_META[tx.type];
    const sign = tx.direction === "+" ? "+" : "-";
    const amountClass = tx.direction === "+" ? "amount-positive" : "amount-negative";
    return `
      <div class="operation-item">
        <div class="d-flex align-items-center gap-2 min-w-0">
          <div class="operation-icon ${meta.className}"><i class="bi ${meta.icon}"></i></div>
          <div class="min-w-0">
            <div class="operation-name">${meta.label}</div>
            <div class="operation-meta">${prettyDate(tx.date)} · ${tx.time}</div>
          </div>
        </div>
        <div>
          <div class="operation-amount ${amountClass}">${sign}${formatMoney(tx.amount)}</div>
          ${tx.fee > 0 ? `<div class="operation-meta text-end">Frais : ${formatMoney(tx.fee)}</div>` : ""}
        </div>
      </div>`;
  }

  function initSidebar() {
    document.querySelectorAll("[data-sidebar-toggle]").forEach((button) => {
      button.addEventListener("click", () => document.body.classList.toggle("sidebar-open"));
    });
    document.querySelector(".sidebar-backdrop")?.addEventListener("click", () => document.body.classList.remove("sidebar-open"));
  }

  function initLogin() {
    const form = document.querySelector("#login-form");
    if (!form) return;
    const input = document.querySelector("#phone");
    const error = document.querySelector("#phone-error");
    const hint = document.querySelector("#prefix-hint");
    hint.textContent = `Préfixes actifs : ${activePrefixes().join(", ")}`;

    form.addEventListener("submit", (event) => {
      event.preventDefault();
      const phone = normalizePhone(input.value);
      if (!isValidPhone(phone)) {
        error.textContent = `Numéro invalide. Préfixes actifs : ${activePrefixes().join(", ")}`;
        error.classList.remove("d-none");
        return;
      }
      error.classList.add("d-none");
      localStorage.setItem(STORAGE.phone, phone);
      ensureClient(phone);
      window.location.href = "client/dashboard.html";
    });
  }

  function initClientDashboard() {
    const container = document.querySelector("#recent-transactions");
    if (!container) return;
    const txs = getTransactions().filter((tx) => tx.client === currentPhone()).slice(0, 4);
    container.innerHTML = txs.length ? txs.map(operationItemHTML).join("") : '<div class="empty-state">Aucune opération pour le moment.</div>';
  }

  function initMoneyForm(type) {
    const form = document.querySelector("#operation-form");
    if (!form) return;

    const amountInput = document.querySelector("#amount");
    const recipientInput = document.querySelector("#recipient");
    const recipientError = document.querySelector("#recipient-error");
    const summary = document.querySelector("#operation-summary");
    const submit = form.querySelector('button[type="submit"]');
    const formView = document.querySelector("#form-view");
    const successView = document.querySelector("#success-view");

    function calculate() {
      const amount = Number(amountInput.value || 0);
      const client = getClient();
      const fee = type === "depot" ? 0 : getFee(type, amount);
      const total = type === "depot" ? amount : amount + fee;
      let recipientValid = true;

      if (type === "transfert") {
        const recipient = normalizePhone(recipientInput.value);
        recipientValid = isValidPhone(recipient) && recipient !== currentPhone();
        const touched = recipientInput.value.trim().length > 0;
        recipientError.classList.toggle("d-none", !touched || recipientValid);
        if (touched && !recipientValid) {
          recipientError.textContent = recipient === currentPhone()
            ? "Vous ne pouvez pas effectuer un transfert vers votre propre numéro."
            : `Numéro invalide. Préfixes actifs : ${activePrefixes().join(", ")}`;
        }
      }

      const enough = type === "depot" || total <= client.balance;
      const validAmount = amount >= 100 && amount <= 2000000;
      submit.disabled = !(validAmount && enough && recipientValid);

      if (!validAmount) {
        summary.classList.add("d-none");
        return;
      }

      summary.classList.remove("d-none", "success", "warning");
      summary.classList.add(type === "depot" ? "success" : type === "retrait" ? "warning" : "");
      const newBalance = type === "depot" ? client.balance + amount : client.balance - total;
      summary.innerHTML = `
        <div class="fw-bold mb-1">Récapitulatif</div>
        <div class="summary-row"><span class="text-mp-muted">Montant</span><strong>${formatMoney(amount)}</strong></div>
        <div class="summary-row"><span class="text-mp-muted">Frais</span><strong>${formatMoney(fee)}</strong></div>
        <div class="summary-row summary-total"><span>${type === "depot" ? "Nouveau solde" : "Total débité"}</span><span>${formatMoney(type === "depot" ? newBalance : total)}</span></div>
        ${type !== "depot" ? `<div class="summary-row"><span class="text-mp-muted">Solde restant</span><strong class="${newBalance < 0 ? "text-danger" : ""}">${formatMoney(newBalance)}</strong></div>` : ""}
        ${!enough ? '<div class="text-danger mt-2"><i class="bi bi-exclamation-triangle me-1"></i>Solde insuffisant.</div>' : ""}`;
    }

    amountInput.addEventListener("input", calculate);
    recipientInput?.addEventListener("input", calculate);
    calculate();

    form.addEventListener("submit", (event) => {
      event.preventDefault();
      const amount = Number(amountInput.value || 0);
      const client = getClient();
      const fee = type === "depot" ? 0 : getFee(type, amount);
      const total = type === "depot" ? amount : amount + fee;
      const recipient = recipientInput ? normalizePhone(recipientInput.value) : null;

      if (amount < 100 || amount > 2000000) return;
      if (type !== "depot" && total > client.balance) return;
      if (type === "transfert" && (!isValidPhone(recipient) || recipient === currentPhone())) return;

      const newBalance = type === "depot" ? client.balance + amount : client.balance - total;
      updateClient(currentPhone(), {
        balance: newBalance,
        operations: client.operations + 1,
        lastActivity: toISODate(new Date()),
        active: true
      });

      if (type === "transfert") {
        const destination = ensureClient(recipient);
        updateClient(recipient, {
          balance: destination.balance + amount,
          operations: destination.operations + 1,
          lastActivity: toISODate(new Date()),
          active: true
        });
      }

      const tx = addTransaction({ type, amount, fee, recipient });
      document.querySelector("#success-title").textContent = type === "depot" ? "Dépôt effectué !" : type === "retrait" ? "Retrait effectué !" : "Transfert envoyé !";
      document.querySelector("#success-amount").textContent = formatMoney(amount);
      document.querySelector("#success-details").textContent = `Frais : ${formatMoney(fee)} · Réf. ${tx.ref}`;
      const destinationLine = document.querySelector("#success-recipient");
      if (destinationLine) {
        destinationLine.textContent = type === "transfert" ? `Vers ${formatPhone(recipient)}` : "";
        destinationLine.classList.toggle("d-none", type !== "transfert");
      }
      formView.classList.add("d-none");
      successView.classList.remove("d-none");
      updateSharedClientUI();
    });

    document.querySelector("#new-operation")?.addEventListener("click", () => {
      form.reset();
      summary.classList.add("d-none");
      successView.classList.add("d-none");
      formView.classList.remove("d-none");
      calculate();
    });
  }

  function initHistory() {
    const list = document.querySelector("#history-list");
    if (!list) return;
    let activeFilter = "tous";

    function render() {
      let txs = getTransactions().filter((tx) => tx.client === currentPhone());
      if (activeFilter !== "tous") txs = txs.filter((tx) => tx.type === activeFilter);
      list.innerHTML = txs.length ? txs.map((tx) => {
        const meta = TYPE_META[tx.type];
        const sign = tx.direction === "+" ? "+" : "-";
        const amountClass = tx.direction === "+" ? "amount-positive" : "amount-negative";
        return `
          <article class="mp-card p-3">
            <div class="d-flex align-items-start justify-content-between gap-3">
              <div class="d-flex align-items-center gap-2">
                <div class="operation-icon ${meta.className}"><i class="bi ${meta.icon}"></i></div>
                <div>
                  <div class="operation-name">${meta.label}</div>
                  <div class="operation-meta">${prettyDate(tx.date)} · ${tx.time}</div>
                </div>
              </div>
              <div class="text-end">
                <div class="operation-amount ${amountClass}">${sign}${formatMoney(tx.amount)}</div>
                ${tx.fee ? `<div class="operation-meta">Frais : ${formatMoney(tx.fee)}</div>` : ""}
              </div>
            </div>
            <div class="d-flex align-items-center gap-2 mt-2 pt-2 border-top">
              <span class="${meta.badge}">${meta.label}</span>
              <span class="operation-meta">Réf. ${tx.ref}</span>
              ${tx.recipient ? `<span class="operation-meta ms-auto">Vers ${formatPhone(tx.recipient)}</span>` : ""}
            </div>
          </article>`;
      }).join("") : '<div class="empty-state">Aucune opération trouvée.</div>';
    }

    document.querySelectorAll("[data-history-filter]").forEach((button) => {
      button.addEventListener("click", () => {
        activeFilter = button.dataset.historyFilter;
        document.querySelectorAll("[data-history-filter]").forEach((item) => item.classList.toggle("active", item === button));
        render();
      });
    });
    render();
  }

  function initAdminDashboard() {
    const table = document.querySelector("#admin-recent-operations");
    if (!table) return;
    const clients = getClients();
    const txs = getTransactions();
    const today = toISODate(new Date());
    const todayTx = txs.filter((tx) => tx.date === today);
    const deposits = todayTx.filter((tx) => tx.type === "depot");
    const withdrawals = todayTx.filter((tx) => tx.type === "retrait");
    const gains = todayTx.reduce((sum, tx) => sum + Number(tx.fee || 0), 0);

    document.querySelector("#stat-clients").textContent = clients.length.toLocaleString("fr-FR");
    document.querySelector("#stat-deposits").textContent = formatMoney(deposits.reduce((sum, tx) => sum + tx.amount, 0));
    document.querySelector("#stat-deposits-sub").textContent = `${deposits.length} transaction(s)`;
    document.querySelector("#stat-withdrawals").textContent = formatMoney(withdrawals.reduce((sum, tx) => sum + tx.amount, 0));
    document.querySelector("#stat-withdrawals-sub").textContent = `${withdrawals.length} transaction(s)`;
    document.querySelector("#stat-gains").textContent = formatMoney(gains);

    table.innerHTML = txs.slice(0, 7).map((tx) => {
      const meta = TYPE_META[tx.type];
      return `<tr>
        <td class="font-monospace text-secondary">${tx.ref}</td>
        <td><span class="${meta.badge}">${meta.label}</span></td>
        <td class="fw-semibold">${formatPhone(tx.client)}</td>
        <td class="text-end fw-semibold">${Number(tx.amount).toLocaleString("fr-FR")}</td>
        <td class="text-end ${tx.fee ? "text-warning-emphasis fw-semibold" : "text-secondary"}">${Number(tx.fee).toLocaleString("fr-FR")}</td>
        <td class="text-secondary">${tx.time}</td>
      </tr>`;
    }).join("");
  }

  function initPrefixes() {
    const tbody = document.querySelector("#prefix-table-body");
    const form = document.querySelector("#prefix-form");
    if (!tbody || !form) return;

    function render() {
      const prefixes = getPrefixes();
      tbody.innerHTML = prefixes.map((item, index) => `<tr>
        <td class="text-secondary">${index + 1}</td>
        <td><span class="font-monospace fw-bold bg-light border rounded px-2 py-1">${item.prefix}</span></td>
        <td>${item.label}</td>
        <td><span class="${item.active ? "badge-soft-success" : "badge-soft-danger"}">${item.active ? "Actif" : "Inactif"}</span></td>
        <td>
          <div class="d-flex flex-wrap gap-1">
            <button class="btn btn-sm btn-outline-secondary" data-prefix-toggle="${item.id}">${item.active ? "Désactiver" : "Activer"}</button>
            <button class="btn btn-sm btn-outline-danger" data-prefix-delete="${item.id}"><i class="bi bi-trash"></i></button>
          </div>
        </td>
      </tr>`).join("");
    }

    form.addEventListener("submit", (event) => {
      event.preventDefault();
      const prefix = normalizePhone(document.querySelector("#new-prefix").value).slice(0, 3);
      const label = document.querySelector("#new-prefix-label").value.trim();
      const prefixes = getPrefixes();
      if (!/^0\d{2}$/.test(prefix)) return showToast("Le préfixe doit contenir 3 chiffres et commencer par 0.", "danger");
      if (prefixes.some((item) => item.prefix === prefix)) return showToast("Ce préfixe existe déjà.", "danger");
      prefixes.push({ id: Date.now(), prefix, label, active: true });
      setPrefixes(prefixes);
      form.reset();
      render();
      showToast("Préfixe ajouté avec succès.");
    });

    tbody.addEventListener("click", (event) => {
      const toggle = event.target.closest("[data-prefix-toggle]");
      const remove = event.target.closest("[data-prefix-delete]");
      if (!toggle && !remove) return;
      const id = Number((toggle || remove).dataset[toggle ? "prefixToggle" : "prefixDelete"]);
      let prefixes = getPrefixes();
      if (toggle) prefixes = prefixes.map((item) => item.id === id ? { ...item, active: !item.active } : item);
      if (remove) prefixes = prefixes.filter((item) => item.id !== id);
      setPrefixes(prefixes);
      render();
      showToast(toggle ? "Statut du préfixe mis à jour." : "Préfixe supprimé.");
    });

    render();
  }

  function initBaremes() {
    const tbody = document.querySelector("#scale-table-body");
    const addForm = document.querySelector("#scale-add-form");
    if (!tbody || !addForm) return;
    let operation = "retrait";

    function render() {
      const rows = getScales()[operation] || [];
      document.querySelector("#scale-title").textContent = `Barème — ${TYPE_META[operation].label}`;
      document.querySelectorAll("[data-operation-tab]").forEach((button) => button.classList.toggle("active", button.dataset.operationTab === operation));
      tbody.innerHTML = rows.map((row, index) => `<tr data-scale-id="${row.id}">
        <td class="text-secondary">${index + 1}</td>
        <td><input class="form-control form-control-sm" type="number" min="0" value="${row.min}" data-field="min"></td>
        <td><input class="form-control form-control-sm" type="number" min="0" value="${row.max}" data-field="max"></td>
        <td><input class="form-control form-control-sm" type="number" min="0" value="${row.fee}" data-field="fee"></td>
        <td>
          <div class="d-flex gap-1">
            <button class="btn btn-sm btn-success" data-scale-save="${row.id}"><i class="bi bi-check2"></i></button>
            <button class="btn btn-sm btn-outline-danger" data-scale-delete="${row.id}" ${rows.length === 1 ? "disabled" : ""}><i class="bi bi-trash"></i></button>
          </div>
        </td>
      </tr>`).join("");
    }

    document.querySelectorAll("[data-operation-tab]").forEach((button) => {
      button.addEventListener("click", () => {
        operation = button.dataset.operationTab;
        render();
      });
    });

    tbody.addEventListener("click", (event) => {
      const save = event.target.closest("[data-scale-save]");
      const remove = event.target.closest("[data-scale-delete]");
      if (!save && !remove) return;
      const id = Number((save || remove).dataset[save ? "scaleSave" : "scaleDelete"]);
      const scales = getScales();
      if (save) {
        const row = save.closest("tr");
        const min = Number(row.querySelector('[data-field="min"]').value);
        const max = Number(row.querySelector('[data-field="max"]').value);
        const fee = Number(row.querySelector('[data-field="fee"]').value);
        if (min < 0 || max < min || fee < 0) return showToast("Vérifiez les valeurs de la tranche.", "danger");
        scales[operation] = scales[operation].map((item) => item.id === id ? { ...item, min, max, fee } : item).sort((a, b) => a.min - b.min);
        setScales(scales);
        showToast("Barème enregistré.");
      } else {
        scales[operation] = scales[operation].filter((item) => item.id !== id);
        setScales(scales);
        showToast("Tranche supprimée.");
      }
      render();
    });

    addForm.addEventListener("submit", (event) => {
      event.preventDefault();
      const min = Number(document.querySelector("#scale-min").value);
      const max = Number(document.querySelector("#scale-max").value);
      const fee = Number(document.querySelector("#scale-fee").value);
      if (min < 0 || max < min || fee < 0) return showToast("Vérifiez les valeurs de la nouvelle tranche.", "danger");
      const scales = getScales();
      scales[operation].push({ id: Date.now(), min, max, fee });
      scales[operation].sort((a, b) => a.min - b.min);
      setScales(scales);
      addForm.reset();
      render();
      showToast("Nouvelle tranche ajoutée.");
    });

    render();
  }

  function initGains() {
    const table = document.querySelector("#gain-table-body");
    if (!table) return;
    let selectedMonth = "Juillet";

    function render() {
      const selected = GAIN_DATA.find((item) => item.month === selectedMonth) || GAIN_DATA.at(-1);
      const total = selected.withdrawal + selected.transfer;
      const withdrawalPercent = total ? selected.withdrawal / total * 100 : 0;
      const transferPercent = 100 - withdrawalPercent;
      document.querySelector("#gain-withdrawal").textContent = formatMoney(selected.withdrawal);
      document.querySelector("#gain-transfer").textContent = formatMoney(selected.transfer);
      document.querySelector("#gain-total").textContent = formatMoney(total);
      document.querySelector("#gain-period-label").textContent = `${selectedMonth} 2026`;
      document.querySelector("#gain-bar-withdrawal").style.width = `${withdrawalPercent}%`;
      document.querySelector("#gain-bar-withdrawal").textContent = `${withdrawalPercent.toFixed(0)}% Retrait`;
      document.querySelector("#gain-bar-transfer").style.width = `${transferPercent}%`;
      document.querySelector("#gain-bar-transfer").textContent = `${transferPercent.toFixed(0)}% Transfert`;
      document.querySelectorAll("[data-month]").forEach((button) => button.classList.toggle("active", button.dataset.month === selectedMonth));
      table.innerHTML = GAIN_DATA.map((item) => {
        const rowTotal = item.withdrawal + item.transfer;
        return `<tr class="${item.month === selectedMonth ? "table-info" : ""}">
          <td class="${item.month === selectedMonth ? "fw-bold" : ""}">${item.month === selectedMonth ? '<i class="bi bi-caret-right-fill text-primary me-1"></i>' : ""}${item.month}</td>
          <td class="text-end fw-semibold text-warning-emphasis">${item.withdrawal.toLocaleString("fr-FR")}</td>
          <td class="text-end fw-semibold text-primary">${item.transfer.toLocaleString("fr-FR")}</td>
          <td class="text-end fw-bold text-success">${rowTotal.toLocaleString("fr-FR")}</td>
        </tr>`;
      }).join("");
    }

    document.querySelectorAll("[data-month]").forEach((button) => {
      button.addEventListener("click", () => { selectedMonth = button.dataset.month; render(); });
    });
    render();
  }

  function initComptes() {
    const tbody = document.querySelector("#client-table-body");
    if (!tbody) return;
    const search = document.querySelector("#client-search");
    let status = "tous";

    function render() {
      const clients = getClients();
      const query = normalizePhone(search.value);
      const filtered = clients.filter((client) => {
        const matchesQuery = !query || client.phone.includes(query);
        const matchesStatus = status === "tous" || (status === "actif" && client.active) || (status === "inactif" && !client.active);
        return matchesQuery && matchesStatus;
      });
      const activeCount = clients.filter((client) => client.active).length;
      document.querySelector("#clients-total").textContent = clients.length.toLocaleString("fr-FR");
      document.querySelector("#clients-active").textContent = `${activeCount} actifs`;
      document.querySelector("#clients-balances").textContent = formatMoney(clients.reduce((sum, client) => sum + client.balance, 0));
      document.querySelector("#clients-inactive").textContent = (clients.length - activeCount).toLocaleString("fr-FR");
      document.querySelector("#clients-count").textContent = `${filtered.length} résultat(s)`;
      document.querySelectorAll("[data-client-status]").forEach((button) => button.classList.toggle("active", button.dataset.clientStatus === status));
      tbody.innerHTML = filtered.length ? filtered.map((client, index) => `<tr>
        <td class="text-secondary">${index + 1}</td>
        <td class="font-monospace fw-bold">${formatPhone(client.phone)}</td>
        <td class="text-end fw-semibold ${client.balance ? "text-success" : "text-secondary"}">${client.balance.toLocaleString("fr-FR")}</td>
        <td class="text-end">${client.operations}</td>
        <td class="text-secondary">${prettyDate(client.lastActivity)}</td>
        <td><span class="${client.active ? "badge-soft-success" : "badge-soft-secondary"}">${client.active ? "Actif" : "Inactif"}</span></td>
      </tr>`).join("") : '<tr><td colspan="6" class="empty-state">Aucun client trouvé.</td></tr>';
    }

    search.addEventListener("input", render);
    document.querySelectorAll("[data-client-status]").forEach((button) => {
      button.addEventListener("click", () => { status = button.dataset.clientStatus; render(); });
    });
    render();
  }

  document.addEventListener("DOMContentLoaded", () => {
    initSidebar();
    updateSharedClientUI();
    const page = document.body.dataset.page;
    if (page === "login") initLogin();
    if (page === "client-dashboard") initClientDashboard();
    if (page === "client-depot") initMoneyForm("depot");
    if (page === "client-retrait") initMoneyForm("retrait");
    if (page === "client-transfert") initMoneyForm("transfert");
    if (page === "client-historique") initHistory();
    if (page === "admin-dashboard") initAdminDashboard();
    if (page === "admin-prefixes") initPrefixes();
    if (page === "admin-baremes") initBaremes();
    if (page === "admin-gains") initGains();
    if (page === "admin-comptes") initComptes();
  });
})();
