<?php
session_start();
$usuario = $_SESSION['usuario'] ?? null;
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dulcen Cup Cakes</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Pacifico&family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
<style>
  :root {
    --fundo: #ffe9f1;
    --rosa: #ee7f96;
    --rosa-escuro: #d85f7b;
    --rosa-claro: #ffd3e1;
    --marrom: #4f4a3a;
    --texto: #4a2c3a;
    --cinza: #8a6f7b;
    --branco: #fff;
  }
  * { box-sizing: border-box; margin: 0; padding: 0; }
  html { scroll-behavior: smooth; }
  body {
    font-family: 'Nunito', sans-serif;
    background: var(--fundo);
    color: var(--texto);
    min-height: 100vh;
    display: flex;
    flex-direction: column;
  }
  a { color: var(--rosa-escuro); font-weight: 700; text-decoration: none; }
  a:hover { text-decoration: underline; }
  button { font-family: inherit; cursor: pointer; }

  /* ---------- Cabeçalho ---------- */
  header {
    position: sticky; top: 0; z-index: 20;
    display: flex; align-items: center; justify-content: space-between;
    padding: 8px 24px;
    background: rgba(255, 233, 241, .92);
    backdrop-filter: blur(8px);
    border-bottom: 2px solid var(--rosa-claro);
  }
  .logo-img { width: 84px; height: 84px; object-fit: contain; display: block; }
  .acoes { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; justify-content: flex-end; }
  .btn {
    border: none; border-radius: 999px; padding: 10px 20px;
    font-weight: 800; font-size: .95rem; transition: transform .15s, box-shadow .15s, background .15s;
  }
  .btn:hover { transform: translateY(-2px); text-decoration: none; }
  .btn-rosa { background: var(--rosa); color: #fff; box-shadow: 0 6px 14px rgba(238,127,150,.4); }
  .btn-rosa:hover { background: var(--rosa-escuro); }
  .btn-contorno { background: #fff; color: var(--rosa-escuro); border: 2px solid var(--rosa); }
  .badge {
    display: inline-block; min-width: 22px; padding: 1px 7px; margin-left: 6px;
    background: #fff; color: var(--rosa-escuro); border-radius: 999px; font-size: .8rem;
  }
  #saudacao { font-weight: 700; font-size: .9rem; color: var(--marrom); }

  /* ---------- Conteúdo ---------- */
  main { width: 100%; max-width: 1100px; margin: 0 auto; padding: 32px 20px 20px; flex: 1; }
  h2 { font-family: 'Pacifico', cursive; font-weight: 400; color: var(--rosa-escuro); font-size: 2rem; margin-bottom: 14px; }

  .sobre {
    background: #fff; border-radius: 24px; padding: 28px 30px;
    box-shadow: 0 10px 30px rgba(238,127,150,.18);
    margin-bottom: 40px;
  }
  .sobre p { line-height: 1.7; margin-bottom: 10px; }
  .sobre p:last-child { margin-bottom: 0; }

  .grade {
    display: grid; gap: 20px;
    grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
  }
  .card {
    background: #fff; border-radius: 20px; padding: 20px 18px 18px; text-align: center;
    box-shadow: 0 8px 22px rgba(238,127,150,.16);
    display: flex; flex-direction: column; align-items: center; gap: 6px;
    transition: transform .2s;
  }
  .card:hover { transform: translateY(-4px); }
  .card img { width: 100%; aspect-ratio: 480 / 439; object-fit: cover; border-radius: 14px; margin-bottom: 6px; }
  .card h3 { font-size: 1.05rem; font-weight: 800; }
  .card .desc { font-size: .85rem; color: var(--cinza); min-height: 2.6em; }
  .card .preco { font-size: 1.25rem; font-weight: 800; color: var(--rosa-escuro); margin: 4px 0 6px; }
  .card .btn { width: 100%; }

  /* ---------- Rodapé ---------- */
  footer {
    display: flex; justify-content: flex-end;
    padding: 20px 24px 24px;
  }
  .contato {
    text-align: right; background: #fff; border-radius: 18px; padding: 16px 22px;
    box-shadow: 0 8px 22px rgba(238,127,150,.16); font-size: .92rem; line-height: 1.7;
  }
  .contato h4 { font-family: 'Pacifico', cursive; font-weight: 400; color: var(--rosa-escuro); font-size: 1.2rem; margin-bottom: 2px; }

  /* ---------- Botão Voltar ao Topo ---------- */
  .btn-topo {
    position: fixed; bottom: 24px; left: 24px; width: 46px; height: 46px;
    background: var(--rosa); color: #fff; border: none; border-radius: 50%;
    font-size: 1.2rem; display: flex; align-items: center; justify-content: center;
    box-shadow: 0 6px 16px rgba(238, 127, 150, 0.4); cursor: pointer; z-index: 40;
    opacity: 0; visibility: hidden; transform: translateY(10px);
    transition: opacity 0.3s, visibility 0.3s, transform 0.3s, background 0.15s;
  }
  .btn-topo.visivel { opacity: 1; visibility: visible; transform: translateY(0); }
  .btn-topo:hover { background: var(--rosa-escuro); transform: translateY(-2px); }

  /* ---------- Carrinho (painel lateral) ---------- */
  .painel {
    position: fixed; top: 0; right: 0; height: 100%; width: 100%; max-width: 380px; background: #fff;
    z-index: 60; transform: translateX(100%); transition: transform .3s;
    display: flex; flex-direction: column; box-shadow: -10px 0 30px rgba(0,0,0,.15);
  }
  .painel.aberto { transform: translateX(0); }
  .painel header { position: static; background: #fff; backdrop-filter: none; padding: 16px 20px; }
  .painel header h3 { font-family: 'Pacifico', cursive; font-weight: 400; color: var(--rosa-escuro); font-size: 1.5rem; }
  .itens { flex: 1; overflow-y: auto; padding: 8px 20px; }
  .item { display: flex; align-items: center; justify-content: space-between; gap: 10px; padding: 12px 0; border-bottom: 1px dashed var(--rosa-claro); }
  .item .nome { font-weight: 700; font-size: .95rem; }
  .item .sub { font-size: .8rem; color: var(--cinza); }
  .qtd { display: flex; align-items: center; gap: 8px; }
  .qtd button { width: 28px; height: 28px; border-radius: 50%; border: 2px solid var(--rosa); background: #fff; color: var(--rosa-escuro); font-weight: 800; line-height: 1; }
  .vazio { text-align: center; color: var(--cinza); padding: 40px 0; }
  .rodape-carrinho { padding: 16px 20px 20px; border-top: 2px solid var(--rosa-claro); }
  .total { display: flex; justify-content: space-between; font-size: 1.2rem; font-weight: 800; margin-bottom: 12px; }
  .rodape-carrinho .btn { width: 100%; }

  .fundo-modal {
    position: fixed; inset: 0; background: rgba(74,44,58,.45);
    display: none; align-items: center; justify-content: center; z-index: 50; padding: 16px;
  }
  .fundo-modal.aberto { display: flex; }

  .aviso {
    position: fixed; bottom: 24px; left: 50%; transform: translateX(-50%) translateY(30px);
    background: var(--marrom); color: #fff; padding: 12px 22px; border-radius: 999px; font-weight: 700; font-size: .9rem;
    opacity: 0; pointer-events: none; transition: all .3s; z-index: 80; max-width: 90%; text-align: center;
  }
  .aviso.visivel { opacity: 1; transform: translateX(-50%) translateY(0); }

  @media (max-width: 520px) {
    header { padding: 6px 12px; }
    .logo-img { width: 64px; height: 64px; }
    .btn { padding: 9px 14px; font-size: .85rem; }
    #saudacao { display: none; }
    footer { justify-content: center; }
    .contato { text-align: center; }
  }
</style>
</head>
<body>

<header>
  <img src="https://i.ibb.co/5gYVr1PL/logo-t.png" alt="logo t" border="0" width="120" height="120" class="logo-img">
  <div class="acoes">
    <button class="btn btn-contorno" id="btn-carrinho">🛒 Carrinho <span class="badge" id="contador">0</span></button>
    <?php if ($usuario): ?>
      <a class="btn btn-contorno" href="pedidos.php">📦 Meus Pedidos</a>
      <span id="saudacao">Olá, <?= htmlspecialchars($usuario['nome'], ENT_QUOTES, 'UTF-8') ?>!</span>
      <a class="btn btn-contorno" href="logout.php">Sair</a>
    <?php else: ?>
      <a class="btn btn-rosa" id="btn-login" href="loguin_page.php">Entrar</a>
    <?php endif; ?>
  </div>
</header>

<main>
  <section class="sobre" id="sobre">
    <h2>Sobre a loja</h2>
    <p>Bem-vindo(a) à <strong>Dulcen Cup Cakes</strong>! Aqui cada cupcake é feito à mão, todos os dias, com ingredientes selecionados e muito carinho. Nossa missão é transformar momentos simples em momentos doces, seja para um presente, uma festa ou aquele cafezinho da tarde.</p>
    <p>Escolha entre nossos vários sabores, monte o seu carrinho e faça seu pedido. Aceitamos encomendas para aniversários, casamentos e eventos corporativos.</p>
  </section>

  <section id="sabores">
    <h2>Nossos sabores</h2>
    <div class="grade" id="grade"></div>
  </section>
</main>

<footer>
  <address class="contato">
    <h4>Contato</h4>
    <div><strong>Endereço:</strong> Rua Açúcar Mascavo, nº 286</div>
    <div><strong>Telefone:</strong> 4002-8922</div>
    <div><strong>E-mail:</strong> <a href="mailto:dulcenCupcake@hotmail.com">dulcenCupcake@hotmail.com</a></div>
  </address>
</footer>

<button class="btn-topo" id="btn-topo" aria-label="Voltar ao topo">▲</button>

<div class="fundo-modal" id="fundo-carrinho"></div>
<aside class="painel" id="painel">
  <header>
    <h3>Seu carrinho</h3>
    <button class="fechar" style="position:static; background:none; border:none; font-size:1.6rem; cursor:pointer;" id="fechar-carrinho" aria-label="Fechar">×</button>
  </header>
  <div class="itens" id="itens"></div>
  <div class="rodape-carrinho">
    <div class="total"><span>Total</span><span id="total">R$ 0,00</span></div>
    <button class="btn btn-rosa" id="finalizar">Finalizar pedido</button>
  </div>
</aside>

<div class="aviso" id="aviso"></div>

<script>
    const IMAGENS = [
    "https://i.ibb.co/XvfcQJN/avela.png",
    "https://i.ibb.co/FLLQCZTV/baunilha.png",
    "https://i.ibb.co/x4P8CRF/chocolate-com-menta.png",
    "https://i.ibb.co/kVwB48D2/chocolate.png",
    "https://i.ibb.co/7JPFGTrC/coco-com-chocolate.png",
    "https://i.ibb.co/svQqZ0x6/doce-de-leite.png",
    "https://i.ibb.co/1G0tqMXz/frutas-vermelhas.png",
    "https://i.ibb.co/LXDGFYnP/lim-o.png",
    "https://i.ibb.co/cSq2dFhn/morango.png",
    "https://i.ibb.co/Y4RgHPYw/pa-oca.png",
    "https://i.ibb.co/VYHKqKfD/pistache.png",
    "https://i.ibb.co/BKHcjt37/red-velvet.png"
];
  const sabores = [
    { nome: "Avelã", preco: 25, desc: "Creme de avelã com pedaços de avelã crocante", img: IMAGENS[0] },
    { nome: "Baunilha", preco: 15, desc: "Clássico com creme de baunilha e granulado", img: IMAGENS[1] },
    { nome: "Chocolate com menta", preco: 21, desc: "Massa de chocolate com creme de menta", img: IMAGENS[2] },
    { nome: "Chocolate", preco: 16, desc: "Massa fofinha com cobertura de chocolate", img: IMAGENS[3] },
    { nome: "Coco com chocolate", preco: 19, desc: "Creme de baunilha, coco ralado e chocolate", img: IMAGENS[4] },
    { nome: "Doce de leite", preco: 19, desc: "Cobertura cremosa de doce de leite", img: IMAGENS[5] },
    { nome: "Frutas vermelhas", preco: 22, desc: "Creme de frutas vermelhas com frutas frescas", img: IMAGENS[6] },
    { nome: "Limão", preco: 18, desc: "Refrescante, com raspas e fatia de limão", img: IMAGENS[7] },
    { nome: "Morango", preco: 17, desc: "Creme de morango com morango fresco", img: IMAGENS[8] },
    { nome: "Paçoca", preco: 20, desc: "Creme de paçoca com paçoca esfarelada", img: IMAGENS[9] },
    { nome: "Pistache", preco: 24, desc: "Creme de pistache com pistaches picados", img: IMAGENS[10] },
    { nome: "Red Velvet", preco: 23, desc: "Massa aveludada com cream cheese", img: IMAGENS[11] },
  ];

  const brl = v => v.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
  const carrinho = {}; 
  const usuarioLogado = <?= $usuario ? 'true' : 'false' ?>;

  const grade = document.getElementById('grade');
  sabores.forEach(s => {
    const el = document.createElement('article');
    el.className = 'card';
    el.innerHTML = `<img src="${s.img}" alt="Cupcake de ${s.nome}" loading="lazy">
      <h3>${s.nome}</h3>
      <p class="desc">${s.desc}</p>
      <div class="preco">${brl(s.preco)}</div>
      <button class="btn btn-rosa">Adicionar ao carrinho</button>`;
    el.querySelector('button').addEventListener('click', () => adicionar(s.nome));
    grade.appendChild(el);
  });

  function adicionar(nome) {
    if (!usuarioLogado) {
      window.location.href = 'loguin_page.php';
      return;
    }
    carrinho[nome] = (carrinho[nome] || 0) + 1;
    atualizar();
    aviso(`${nome} adicionado ao carrinho 🧁`);
  }

  function alterar(nome, delta) {
    carrinho[nome] = (carrinho[nome] || 0) + delta;
    if (carrinho[nome] <= 0) delete carrinho[nome];
    atualizar();
  }

  function atualizar() {
    const itens = document.getElementById('itens');
    const nomes = Object.keys(carrinho);
    let total = 0, qtdTotal = 0;
    if (!nomes.length) {
      itens.innerHTML = '<div class="vazio">Seu carrinho está vazio 🧁</div>';
    } else {
      itens.innerHTML = '';
      nomes.forEach(nome => {
        const s = sabores.find(x => x.nome === nome);
        const q = carrinho[nome];
        total += s.preco * q; qtdTotal += q;
        const row = document.createElement('div');
        row.className = 'item';
        row.innerHTML = `<div><div class="nome">${nome}</div><div class="sub">${brl(s.preco)} cada · ${brl(s.preco * q)}</div></div>
          <div class="qtd"><button aria-label="Diminuir">−</button><strong>${q}</strong><button aria-label="Aumentar">+</button></div>`;
        const [menos, mais] = row.querySelectorAll('button');
        menos.onclick = () => alterar(nome, -1);
        mais.onclick = () => alterar(nome, +1);
        itens.appendChild(row);
      });
    }
    document.getElementById('total').textContent = brl(total);
    document.getElementById('contador').textContent = qtdTotal;
  }

  const painel = document.getElementById('painel');
  const fundoCarrinho = document.getElementById('fundo-carrinho');
  
  function abrirCarrinho()  { 
    if (!usuarioLogado) {
      window.location.href = 'loguin_page.php';
      return;
    }
    painel.classList.add('aberto');    
    fundoCarrinho.classList.add('aberto'); 
  }
  function fecharCarrinho() { painel.classList.remove('aberto'); fundoCarrinho.classList.remove('aberto'); }
  
  document.getElementById('btn-carrinho').onclick = abrirCarrinho;
  document.getElementById('fechar-carrinho').onclick = fecharCarrinho;
  fundoCarrinho.onclick = fecharCarrinho;

  // Finalizar pedido salvando no banco de dados
  document.getElementById('finalizar').onclick = () => {
    if (!Object.keys(carrinho).length) return aviso('Adicione ao menos um cupcake 🧁');

    const itensArray = Object.keys(carrinho).map(nome => {
      const s = sabores.find(x => x.nome === nome);
      return { nome: s.nome, preco: s.preco, quantidade: carrinho[nome] };
    });

    fetch('salvar_pedido.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ itens: itensArray })
    })
    .then(res => res.json())
    .then(data => {
      if (data.sucesso) {
        Object.keys(carrinho).forEach(k => delete carrinho[k]);
        atualizar(); 
        fecharCarrinho();
        aviso('Pedido realizado com sucesso! Veja em "Meus Pedidos" 💖');
      } else {
        aviso(data.mensagem || 'Erro ao finalizar pedido.');
      }
    })
    .catch(() => {
      aviso('Erro de conexão ao salvar pedido.');
    });
  };

  const btnTopo = document.getElementById('btn-topo');
  window.addEventListener('scroll', () => {
    if (window.scrollY > 300) {
      btnTopo.classList.add('visivel');
    } else {
      btnTopo.classList.remove('visivel');
    }
  });
  btnTopo.addEventListener('click', () => {
    window.scrollTo({ top: 0, behavior: 'smooth' });
  });

  let timer;
  function aviso(msg) {
    const el = document.getElementById('aviso');
    el.textContent = msg; el.classList.add('visivel');
    clearTimeout(timer); timer = setTimeout(() => el.classList.remove('visivel'), 2200);
  }

  atualizar();
</script>
</body>
</html>