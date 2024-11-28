CREATE TABLE IF NOT EXISTS TBSETORES(
	idsetor SERIAL PRIMARY KEY NOT NULL,
	nome VARCHAR(30) NOT NULL,
	descricao TEXT NOT NULL	
);

CREATE TABLE IF NOT EXISTS TBDISPOSITIVOS(
	iddispositivo SERIAL PRIMARY KEY NOT NULL,
	nomedispositivo VARCHAR(40) NOT NULL,
	status bool
);

CREATE TABLE TBQUESTOES (
    idquestao SERIAL PRIMARY KEY NOT NULL,
    idsetor INTEGER NOT NULL,
    numquestao INTEGER NOT NULL,
    pergunta TEXT NOT NULL,
    statuspergunta BOOLEAN,
    CONSTRAINT "TBQUESTOES_TBSETORES" FOREIGN KEY (idsetor) REFERENCES TBSETORES(idsetor),
    CONSTRAINT uq_setor_numquestao UNIQUE (idsetor, numquestao)
);

CREATE OR REPLACE FUNCTION generate_sequencial()
RETURNS TRIGGER AS $$
BEGIN
    NEW.numquestao := COALESCE(
        (SELECT MAX(numquestao) + 1 FROM TBQUESTOES WHERE idsetor = NEW.idsetor),
        1
    );
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER tg_generate_sequencial
BEFORE INSERT ON TBQUESTOES
FOR EACH ROW
EXECUTE FUNCTION generate_sequencial();

CREATE TABLE IF NOT EXISTS TBUSER(
	iduser SERIAL PRIMARY KEY NOT NULL,
	name VARCHAR(100),
	idsetor INT NOT NULL,
	CONSTRAINT "TBSETOR->TBUSER" FOREIGN KEY (IDSETOR) REFERENCES TBSETORES(IDSETOR)
);

CREATE TABLE IF NOT EXISTS tbadmin(
	iduser INT  NOT NULL,
	pass TEXT,
	CONSTRAINT "TBUSER -> TBADMIN" FOREIGN KEY (IDUSER) REFERENCES TBUSER(IDUSER)
);
 
CREATE TABLE IF NOT EXISTS TBAVALIACOES(
	idavaliacao SERIAL PRIMARY KEY NOT NULL,
	idsetor INT NOT NULL,
	idquestao INT NOT NULL,
	iddispositivo INT NOT NULL,
	notaquestao INT NOT NULL,
	feedback text,
	datahora TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
	CONSTRAINT "TBAVALIACOES -> TBSETORES" FOREIGN KEY (idsetor) REFERENCES TBSETORES(idsetor),
	CONSTRAINT "TBAVALIACOES -> TBQUESTOES" FOREIGN KEY (idquestao) REFERENCES TBQUESTOES(idquestao),
	CONSTRAINT "TBAVALIACOES -> TBDISPOSITIVOS" FOREIGN KEY (iddispositivo) REFERENCES TBDISPOSITIVOS(iddispositivo)
);

CREATE TABLE IF NOT EXISTS TBFUNCIONARIOS(
	IDFUNCIONARIO SERIAL NOT NULL PRIMARY KEY,
	IDSETOR INT NOT NULL,
	NOME VARCHAR(60),
	DESCRICAO TEXT,
	CONSTRAINT "TBFUNCIONARIOS -> TBSETORES" FOREIGN KEY (idsetor) REFERENCES TBSETORES(idsetor)
);

CREATE TABLE IF NOT EXISTS TBSETORESQUESTOES(
	IDSETOR INT NOT NULL,
	IDQUESTAO INT NOT NULL,
	CONSTRAINT "REL TBSETORESQUESTOES -> TBSETORES" FOREIGN KEY (idsetor) REFERENCES TBSETORES(idsetor),
	CONSTRAINT "REL TBSETORESQUESTOES -> TBQUESTOES" FOREIGN KEY (idquestao) REFERENCES TBQUESTOES(idquestao)
);

INSERT INTO TBSETORES(NOME,DESCRICAO)
	VALUES 
	('Recepção','A recepção de um hospital é o ponto de entrada e primeiro contato do paciente com a instituição. É nesse local que o paciente é acolhido, registrado e direcionado para o serviço adequado.'),
	('Enfermagem','Um quarto ou conjunto de quartos em um hospital onde os pacientes internados recebem cuidados.'),
	('Emergência','A emergência hospitalar é o setor de um hospital destinado a prestar atendimento imediato a pacientes com condições agudas ou que necessitem de cuidados médicos urgentes. É um local com equipe multidisciplinar preparada para lidar com diversas situações de emergência, desde traumas e acidentes até doenças agudas que exigem intervenção médica rápida.'),
	('Alimentação','Relacionado ao alimento oferecido pelo estabelecimento ou até mesmo a praça de alimentaçõa caso esse mesmo tenha.'),
	('Ambulância','Ambulância é um veículo equipado para o transporte ou prestação de primeiros socorros a doentes e feridos');

INSERT INTO TBQUESTOES(IDSETOR,PERGUNTA,STATUSPERGUNTA)
	VALUES
	(1,'Você foi atendido com rapidez na recepção?',TRUE),
	(1,'Os funcionários da recepção foram educados e prestativos?',TRUE),
	(1,'As informações sobre o seu agendamento foram precisas?',TRUE),
	(1,'Você encontrou facilmente a localização do seu atendimento?',TRUE),
	(1,'O ambiente da recepção era agradável e confortável?',TRUE),
	(1,'Você se sentiu bem-vindo ao chegar ao hospital?',TRUE),
	(1,'Suas dúvidas foram esclarecidas na recepção?',TRUE),
	(1,'O processo de check-in foi rápido e fácil?',TRUE),
	(1,'Você recebeu todas as informações necessárias para o seu atendimento?',TRUE),
	(1,'Você se sentiu seguro ao fornecer seus dados pessoais na recepção?',TRUE),
	(2,'Os enfermeiros foram atenciosos e cuidadosos durante todo o tratamento?',TRUE),
	(2,'Os enfermeiros explicaram os procedimentos de forma clara e simples?',TRUE),
	(2,'Os enfermeiros foram eficazes em controlar sua dor?',TRUE),
	(2,'O ambiente do quarto e os equipamentos utilizados estavam limpos?',TRUE),
	(2,'Os enfermeiros estavam disponíveis quando você precisou?',TRUE),
	(2,'Os enfermeiros trataram você com respeito e dignidade?',TRUE),
	(2,'Você se sentiu confortável durante a sua estadia?',TRUE),
	(2,'Você recebeu todas as informações necessárias sobre o seu tratamento?',TRUE),
	(2,'Os enfermeiros te ajudaram a se sentir mais tranquilo durante o tratamento?',TRUE),
	(2,'Sua privacidade foi respeitada durante o atendimento?',TRUE),
	(3,'O atendimento inicial foi rápido e eficiente?',TRUE),
	(3,'O diagnóstico do seu problema de saúde foi preciso?',TRUE),
	(3,'O tratamento recebido foi eficaz para aliviar seus sintomas?',TRUE),
	(3,'Você recebeu informações claras sobre o seu estado de saúde e o tratamento?',TRUE),
	(3,'Os profissionais da emergência te ajudaram a se manter calmo durante a situação?',TRUE),
	(3,'A dor foi controlada de forma adequada?',TRUE),
	(3,'Você se sentiu respeitado durante todo o atendimento?',TRUE),
	(3,'O ambiente da emergência era organizado e limpo?',TRUE),
	(3,'Você se sentiu seguro durante o atendimento?',TRUE),
	(3,'As instruções para os cuidados em casa foram claras e completas?',TRUE),
	(4,'A comida servida era saborosa?',TRUE),
	(4,'A variedade de alimentos oferecidos era satisfatória?',TRUE),
	(4,'As refeições eram servidas na temperatura adequada?',TRUE),
	(4,'A apresentação dos alimentos era agradável?',TRUE),
	(4,'A quantidade de comida servida era suficiente?',TRUE),
	(4,'Os horários das refeições eram convenientes?',TRUE),
	(4,'Você recebeu informações sobre a composição nutricional dos alimentos?',TRUE),
	(4,'A higiene no local onde as refeições eram preparadas era adequada?',TRUE),
	(4,'Você se sentiu satisfeito com a qualidade da alimentação?',TRUE),
	(4,'Você teria alguma sugestão para melhorar a alimentação?',TRUE),
	(5,'O tempo de espera pela ambulância foi adequado?',FALSE),
	(5,'Os profissionais da ambulância foram atenciosos e cuidadosos?',FALSE),
	(5,'Você se sentiu seguro durante o transporte?',FALSE),
	(5,'O equipamento utilizado na ambulância parecia estar em bom estado?',FALSE),
	(5,'As informações sobre o seu estado de saúde foram transmitidas claramente para o hospital?',FALSE),
	(5,'Você recebeu os primeiros socorros necessários durante o transporte?',FALSE),
	(5,'O ambiente da ambulância era limpo e organizado?',FALSE),
	(5,'Você se sentiu confortável durante o transporte?',FALSE),
	(5,'As informações sobre o seu destino foram claras?',FALSE),
	(5,'Você se sentiu seguro durante todo o atendimento da ambulância?',FALSE);

INSERT INTO TBDISPOSITIVOS(NOMEDISPOSITIVO,STATUS)
    VALUES
	('Dispositivo recepção',true),
	('Dispositivo enfermagem',true),
	('Dispositivo emergência',true),
	('Dispositivo alimentação',true),
	('Dispositivo ambulância',false);