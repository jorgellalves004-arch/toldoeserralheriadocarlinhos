-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 19-Nov-2025 às 14:10
-- Versão do servidor: 10.4.32-MariaDB
-- versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `serralheria`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `apresentacao_empresa`
--

CREATE TABLE `apresentacao_empresa` (
  `id` int(11) NOT NULL,
  `titulo` varchar(200) NOT NULL,
  `conteudo` text NOT NULL,
  `secao` enum('historia','missao','valores','equipe','servicos') NOT NULL,
  `data_publicacao` timestamp NOT NULL DEFAULT current_timestamp(),
  `autor` varchar(100) DEFAULT NULL,
  `status` enum('ativo','inativo') DEFAULT 'ativo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `comentarios_imagens`
--

CREATE TABLE `comentarios_imagens` (
  `id` int(11) NOT NULL,
  `imagem` varchar(255) NOT NULL,
  `nome_usuario` varchar(100) NOT NULL,
  `comentario` text NOT NULL,
  `data` timestamp NOT NULL DEFAULT current_timestamp(),
  `estrelas` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `empresa`
--

CREATE TABLE `empresa` (
  `id` int(11) NOT NULL,
  `cnpj` varchar(18) NOT NULL,
  `razao_social` varchar(255) NOT NULL,
  `nome_fantasia` varchar(255) DEFAULT NULL,
  `data_abertura` date DEFAULT NULL,
  `natureza_juridica` varchar(255) DEFAULT NULL,
  `situacao` varchar(100) DEFAULT NULL,
  `situacao_especial` varchar(100) DEFAULT NULL,
  `tipo_unidade` varchar(50) DEFAULT NULL,
  `opcao_simples` varchar(100) DEFAULT NULL,
  `enquadramento_porte` varchar(50) DEFAULT NULL,
  `capital_social` decimal(15,2) DEFAULT NULL,
  `opcao_mei` varchar(10) DEFAULT NULL,
  `optante_simples` varchar(10) DEFAULT NULL,
  `data_opcao_simples` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;

--
-- Extraindo dados da tabela `empresa`
--

INSERT INTO `empresa` (`id`, `cnpj`, `razao_social`, `nome_fantasia`, `data_abertura`, `natureza_juridica`, `situacao`, `situacao_especial`, `tipo_unidade`, `opcao_simples`, `enquadramento_porte`, `capital_social`, `opcao_mei`, `optante_simples`, `data_opcao_simples`) VALUES
(1, '12.345.678/0001-90', 'Padaria Pão Quente Ltda.', 'Padaria Pão Quente', '2020-05-10', 'Sociedade Empresária Limitada (206-2)', 'ATIVA desde 10/05/2020', 'Não Disponível', 'MATRIZ', 'OPTANTE desde 10/05/2020', 'ME', 120000.00, 'Não', 'OPTANTE', '2020-05-10');

-- --------------------------------------------------------

--
-- Estrutura da tabela `estatisticas`
--

CREATE TABLE `estatisticas` (
  `id` int(11) NOT NULL,
  `acessos` int(11) DEFAULT 0,
  `contatos` int(11) DEFAULT 0,
  `servicos_finalizados` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `trabalhos`
--

CREATE TABLE `trabalhos` (
  `id_trabalho` int(11) NOT NULL,
  `nome_trabalho` varchar(255) NOT NULL,
  `data_inicio` date NOT NULL,
  `data_fim` date DEFAULT NULL,
  `imagem` varchar(255) NOT NULL,
  `categoria` varchar(100) NOT NULL,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp(),
  `estrelas` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `usuarios_admin`
--

CREATE TABLE `usuarios_admin` (
  `id` int(11) NOT NULL,
  `usuario` varchar(100) NOT NULL,
  `senha` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;

--
-- Índices para tabelas despejadas
--

--
-- Índices para tabela `apresentacao_empresa`
--
ALTER TABLE `apresentacao_empresa`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `comentarios_imagens`
--
ALTER TABLE `comentarios_imagens`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `empresa`
--
ALTER TABLE `empresa`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cnpj` (`cnpj`);

--
-- Índices para tabela `estatisticas`
--
ALTER TABLE `estatisticas`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `trabalhos`
--
ALTER TABLE `trabalhos`
  ADD PRIMARY KEY (`id_trabalho`);

--
-- Índices para tabela `usuarios_admin`
--
ALTER TABLE `usuarios_admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `usuario` (`usuario`);

--
-- AUTO_INCREMENT de tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `apresentacao_empresa`
--
ALTER TABLE `apresentacao_empresa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `comentarios_imagens`
--
ALTER TABLE `comentarios_imagens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- AUTO_INCREMENT de tabela `empresa`
--
ALTER TABLE `empresa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `estatisticas`
--
ALTER TABLE `estatisticas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `trabalhos`
--
ALTER TABLE `trabalhos`
  MODIFY `id_trabalho` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT de tabela `usuarios_admin`
--
ALTER TABLE `usuarios_admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
