<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nossa História - TOLDO E SERRALHERIA DO CARLINHO</title>
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: #f8f9fa;
            color: #333;
            line-height: 1.6;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        header {
            background: linear-gradient(135deg, #2c3e50, #1a2530);
            color: white;
            padding: 30px 0;
            text-align: center;
            border-bottom: 5px solid #3498db;
            margin-bottom: 30px;
        }
        
        h1 {
            font-size: 2.5rem;
            margin-bottom: 10px;
        }
        
        .subtitle {
            font-size: 1.2rem;
            opacity: 0.9;
        }
        
        .intro {
            background-color: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
            text-align: center;
        }
       /* Layout vertical da tabela em telas menores */
@media (max-width: 768px) {
  

  .info-table {
    display: block;
    
    border: none;
    box-shadow: none;
    margin: 0 auto;
  }

  .info-table thead {
    display: none; /* esconde cabeçalho */
  }

  .info-table tbody tr {
    display: block;
    background: white;
    margin-bottom: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
    border: 1px solid #ddd;
    padding: 10px;
  }

  .info-table tbody td {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px ;
    border: none;
    border-bottom: 1px solid #eee;
    word-break: break-word; /* quebra texto longo */

    width: 355px;
  }

  .info-table tbody td:last-child {
    border-bottom: none;
  }

  /* Rótulos simulando cabeçalhos */
  .info-table tbody td:nth-child(1)::before { content: "Seção:"; font-weight: bold; color: #3498db; }
  .info-table tbody td:nth-child(2)::before { content: "Título:"; font-weight: bold; color: #3498db; }
  .info-table tbody td:nth-child(3)::before { content: "Conteúdo:"; font-weight: bold; color: #3498db; }
  .info-table tbody td:nth-child(4)::before { content: "Data:"; font-weight: bold; color: #3498db; }

  .info-table tbody td::before {
    margin-right: 10px;
    flex-shrink: 0;
  }

    
}


        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin: 25px 0;
            font-size: 1rem;
            min-width: 400px;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        
        .info-table thead tr {
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: white;
            text-align: left;
            font-weight: bold;
        }
        
        .info-table th,
        .info-table td {
            padding: 16px 20px;
            text-align: left;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .info-table tbody tr {
            transition: background-color 0.3s;
        }
        
        .info-table tbody tr:nth-of-type(even) {
            background-color: #f8f9fa;
        }
        
        .info-table tbody tr:last-of-type {
            border-bottom: 3px solid #3498db;
        }
        
        .info-table tbody tr:hover {
            background-color: #e8f4fc;
            cursor: pointer;
        }
        
        .highlight {
            background-color: #e8f4fc;
            font-weight: 600;
        }
        
        .footer {
            text-align: center;
            margin-top: 40px;
            padding: 20px;
            color: #7f8c8d;
            font-size: 0.9rem;
        }
        
        .message {
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
            text-align: center;
        }
        
        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        @media (max-width: 768px) {
            .info-table {
                display: block;
                overflow-x: auto;
            }
            
            h1 {
                font-size: 2rem;
            }
        }
      .imglogo {
  width: 100px;
  height: 100px;
  border-radius: 60%;
  object-fit: cover;
  border: 2px solid #3498db;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);
  position: absolute;
  top: 20px;
  right: 40px;
}

/* Ajuste para telas pequenas */
@media (max-width: 768px) {
  .imglogo {
    position: static; /* remove posição absoluta */
    display: block;
    margin: 15px auto 0 auto; /* centraliza a logo */
    width: 90%; /* ocupa 90% da largura da tela */
    max-width: 300px; /* não passa de 300px */
    height: auto; /* mantém proporção original */
    border-radius: 0%;
    object-fit: contain; /* mostra a imagem inteira sem cortar */
  }
}


  header {
    padding: 20px 10px;
  }

  h1 {
    font-size: 1.6rem;
  }

  .subtitle {
    font-size: 1rem;
  }


    </style>
</head>
<body>
    <header>
        <div class="container">
            <h1>Nossa História - TOLDO E SERRALHERIA DO CARLINHO</h1>
            <p class="subtitle">Conheça mais sobre nossa trajetória e valores</p>
        </div>
        <div class="header-right">
            <img class="imglogo" src="../imagens/logo.jpg" alt="Logo">
        </div>
    </header>
    
    <div class="container">
        <div class="intro">
            <h2>Bem-vindo à Nossa Serralheria</h2>
            <p>Há mais de uma década, temos orgulho de oferecer soluções inovadoras e serviços de qualidade em serralheria para nossos clientes. Conheça abaixo os principais aspectos que fazem da nossa empresa uma referência no mercado.</p>
        </div>
        
        <?php
        // Incluir o arquivo de configuração
        include 'config.php';
        
        // Verificar se a conexão foi bem sucedida
        if ($conn->connect_errno) {
            echo "<div class='message error'>Erro de conexão: " . $conn->connect_error . "</div>";
        } else {
            // Consulta SQL para buscar os dados
            $sql = "SELECT titulo, conteudo, secao, data_publicacao, autor FROM apresentacao_empresa WHERE status = 'ativo' ORDER BY secao, data_publicacao DESC";
            $result = $conn->query($sql);
            
            if ($result && $result->num_rows > 0) {
                echo "<table class='info-table'>";
                echo "<thead>
                        <tr>
                            <th width='20%'>Seção</th>
                            <th width='25%'>Título</th>
                            <th width='45%'>Conteúdo</th>
                            <th width='10%'>Data</th>
                        </tr>
                    </thead>
                    <tbody>";
                
                // Exibir os dados de cada linha
                $count = 0;
                while($row = $result->fetch_assoc()) {
                    $highlightClass = ($count % 3 == 0) ? 'highlight' : '';
                    echo "<tr class='$highlightClass'>
                            <td><strong>" . htmlspecialchars($row["secao"]) . "</strong></td>
                            <td>" . htmlspecialchars($row["titulo"]) . "</td>
                            <td>" . htmlspecialchars($row["conteudo"]) . "</td>
                            <td>" . date('d/m/Y', strtotime($row["data_publicacao"])) . "</td>
                          </tr>";
                    $count++;
                }
                
                echo "</tbody></table>";
            } else {
                echo "<div class='message error'>Nenhuma informação encontrada na base de dados.</div>";
                
                // Dados de exemplo para demonstração
                $empresa_info = array(
                    array("historia", "Nossa Fundação", "Iniciamos nossas atividades em 2010 com foco em inovação e qualidade em serralheria.", "01/01/2010"),
                    array("missao", "Nossa Missão", "Proporcionar produtos e serviços em metalurgia que superem as expectativas dos clientes.", "15/03/2012"),
                    array("valores", "Nossos Valores", "Qualidade, compromisso com prazos e atendimento personalizado são nossos pilares.", "20/05/2015")
                );
                
                echo "<table class='info-table'>";
                echo "<thead>
                        <tr>
                            <th width='20%'>Seção</th>
                            <th width='25%'>Título</th>
                            <th width='45%'>Conteúdo</th>
                            <th width='10%'>Data</th>
                        </tr>
                    </thead>
                    <tbody>";
                
                $count = 0;
                foreach ($empresa_info as $info) {
                    $highlightClass = ($count % 3 == 0) ? 'highlight' : '';
                    echo "<tr class='$highlightClass'>
                            <td><strong>" . htmlspecialchars($info[0]) . "</strong></td>
                            <td>" . htmlspecialchars($info[1]) . "</td>
                            <td>" . htmlspecialchars($info[2]) . "</td>
                            <td>" . htmlspecialchars($info[3]) . "</td>
                          </tr>";
                    $count++;
                }
                
                echo "</tbody></table>";
                echo "<div class='message success'>Estes são dados de exemplo. Quando adicionar informações reais na tabela 'apresentacao_empresa', elas aparecerão aqui.</div>";
            }
            
            // Fechar conexão
            $conn->close();
        }
        ?>
        
        <div class="footer">
            <p>© 2023 TOLDO E SERRALHERIA DO CARLINHO - Todos os direitos reservados</p>
            <p>Contato: contato@serralheria.com | (11) 3456-7890</p>
        </div>
    </div>
</body>
</html>