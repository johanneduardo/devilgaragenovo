<?php
require 'verifica.php';

if (isset($_SESSION['idUser']) && !empty($_SESSION['idUser'])):
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customização Harley</title>
    <style>
        .form-container {
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 10px;
        }

        .custom-item {
            margin: 10px 0;
        }

        .custom-item-inline {
            display: inline-block;
            margin-right: 20px;
        }

        .total-cost {
            font-weight: bold;
            font-size: 20px;
            margin-top: 20px;

        
        }
    </style>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body>
<nav class="navbar navbar-expand-lg bg-body-tertiary">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">Devil's Garage</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarText" aria-controls="navbarText" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarText">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="#">Inicio</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Cadastro</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Customização</a>
                </li>
            </ul>
            <div class="form2.0">
                <label class="mr-"><?php echo $nomeUser; ?></label>
                <a href="logout.php">SAIR</a>
            </div>
        </div>
    </div>
</nav>

<main class="menu-urls">
    <div class="form-container">
        <form id="customForm">
            <div class="mb-3">
                <label for="moto" class="form-label">Escolha sua Harley:</label>
                <select class="form-select" id="moto">
                    <option value="0">Selecione uma moto</option>
                    <option value="10000">Harley Davidson ULTRA LIMITED - R$10.000</option>
                    <option value="15000">Harley Davidson GLIDE - R$15.000</option>
                    <option value="20000">Harley Davidson ROAD GLIDE  - R$20.000</option>
                    <option value="25000">Harley Davidson STREET GLIDE - R$25.000</option>
                    <option value="30000">Harley Davidson XL 1200 - R$30.000</option>
                </select>
            </div>

            <div class="custom-item">
                <input type="checkbox" id="quadro" value="3000">
                <label for="quadro">Quadro - R$3.000</label>
            </div>

            <div class="custom-item-inline">
                <input type="radio" id="banco_iron" name="banco" value="1000">
                <label for="banco_iron">Banco Iron - R$1.000</label>
            </div>
            <div class="custom-item-inline">
                <input type="radio" id="banco_low_classic" name="banco" value="1500">
                <label for="banco_low_classic">Banco Low Classic - R$1.500</label>
            </div>

            <div class="custom-item">
                <input type="checkbox" id="guidao" value="1000">
                <label for="guidao">Guidão - R$1.000</label>
            </div>
            <div class="custom-item">
                <input type="checkbox" id="motor" value="5000">
                <label for="motor">Motor - R$5.000</label>
            </div>

            <div class="total-cost" id="totalCost">Valor total: R$0,00</div>

            <div class="button-container">
                <button type="button" class="btn btn-success btn-custom" onclick="comprar()">Comprar</button>
                <button type="button" class="btn btn-danger btn-custom" onclick="excluir()">Excluir</button>
            </div>
        </form>
    </div>
</main>

<footer class="footer">
    <h3>© 2024 Criado por João Henrique Marquez</h3>
</footer>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const motoSelect = document.getElementById('moto');
        const customItems = document.querySelectorAll('.custom-item input, .custom-item-inline input');
        const totalCostElement = document.getElementById('totalCost');

        function updateTotalCost() {
            let totalCost = parseFloat(motoSelect.value) || 0;

            customItems.forEach(item => {
                if (item.checked) {
                    totalCost += parseFloat(item.value);
                }
            });

            totalCostElement.textContent = 'Valor total: R$' + totalCost.toLocaleString('pt-BR', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }

        motoSelect.addEventListener('change', updateTotalCost);
        customItems.forEach(item => {
            item.addEventListener('change', updateTotalCost);
        });

        function comprar() {
            const items = [];
            customItems.forEach(item => {
                if (item.checked) {
                    items.push({ name: item.nextElementSibling.textContent, value: parseFloat(item.value) });
                }
            });

            fetch('registrarCompra.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ moto: parseFloat(motoSelect.value), items })
            }).then(response => response.json()).then(data => {
                if (data.success) {
                    alert("Compra realizada com sucesso! Valor total: " + totalCostElement.textContent);
                } else {
                    alert("Falha na compra: " + data.message);
                }
            });
        }

        function excluir() {
            const selectedItems = document.querySelectorAll('.custom-item input:checked, .custom-item-inline input:checked');
            const items = [];

            selectedItems.forEach(item => {
                items.push({ name: item.nextElementSibling.textContent.trim(), value: parseFloat(item.value) });
            });

            fetch('registrarExclusao.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ items: items })
            }).then(response => response.json()).then(data => {
                if (data.success) {
                    // Limpar seleções e atualizar custo total
                    document.getElementById('moto').value = "0";
                    selectedItems.forEach(item => {
                        item.checked = false;
                    });
                    updateTotalCost();
                    alert("Itens excluídos com sucesso!");
                } else {
                    alert("Falha ao excluir itens: " + data.message);
                }
            }).catch(error => {
                console.error('Erro ao enviar requisição:', error);
            });
        }

        window.comprar = comprar;
        window.excluir = excluir;
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>

<?php
else:
    header("Location: criarCadastro.php");
endif;
?>
