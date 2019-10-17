--
-- Database: `web_system`
--

CREATE DATABASE IF NOT EXISTS `web_system`;
use `web_system`;
-- --------------------------------------------------------

--
-- Estrutura da tabela `usuario`
--

CREATE TABLE `usuario` (
  `id` int(25) NOT NULL,
  `primeiro_nome` varchar(30) CHARACTER SET utf8 NOT NULL,
  `sobrenome` varchar(90) DEFAULT NULL
);

--
-- Extraindo dados da tabela `usuario`
--

INSERT INTO `usuario` (`id`, `primeiro_nome`, `sobrenome`) VALUES
(1, 'Filipe', 'klinger'),
(2, 'James', 'klinger');

--
-- Indexes for table `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`);


--
-- AUTO_INCREMENT for table `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id` int(25) NOT NULL AUTO_INCREMENT;
