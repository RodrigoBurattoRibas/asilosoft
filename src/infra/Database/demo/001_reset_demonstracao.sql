DELETE FROM movimentacoes_estoque;

DELETE FROM usos_materiais;

DELETE FROM solicitacoes_compra;

DELETE FROM registros_saude;

DELETE FROM idosos_familiares;

DELETE FROM familiares;

DELETE FROM idosos;

DELETE FROM estoques;

DELETE FROM materiais;

DELETE FROM quartos;

DELETE FROM logs_auditoria;

DELETE FROM usuarios WHERE id <> @demo_admin;

INSERT INTO usuarios(nome,email,senha_hash,perfil) VALUES ('Mariana Costa','saude@asilosoft.local',@demo_hash_saude,'Profissional de Saúde');

SET @demo_saude = LAST_INSERT_ID();

INSERT INTO usuarios(nome,email,senha_hash,perfil) VALUES ('Carlos Almeida','almoxarife@asilosoft.local',@demo_hash_almoxarife,'Almoxarife');

SET @demo_almoxarife = LAST_INSERT_ID();

INSERT INTO usuarios(nome,email,senha_hash,perfil) VALUES ('Ana Oliveira','familiar@asilosoft.local',@demo_hash_familiar,'Familiar');

SET @demo_familiar = LAST_INSERT_ID();

INSERT INTO quartos(codigo,capacidade,ativo) VALUES ('A-01',4,1);

SET @demo_quarto_1 = LAST_INSERT_ID();

INSERT INTO quartos(codigo,capacidade,ativo) VALUES ('A-02',4,1);

SET @demo_quarto_2 = LAST_INSERT_ID();

INSERT INTO quartos(codigo,capacidade,ativo) VALUES ('A-03',4,1);

SET @demo_quarto_3 = LAST_INSERT_ID();

INSERT INTO quartos(codigo,capacidade,ativo) VALUES ('A-04',4,1);

SET @demo_quarto_4 = LAST_INSERT_ID();

INSERT INTO quartos(codigo,capacidade,ativo) VALUES ('A-05',4,1);

SET @demo_quarto_5 = LAST_INSERT_ID();

INSERT INTO quartos(codigo,capacidade,ativo) VALUES ('A-06',4,1);

SET @demo_quarto_6 = LAST_INSERT_ID();

INSERT INTO quartos(codigo,capacidade,ativo) VALUES ('A-07',4,1);

SET @demo_quarto_7 = LAST_INSERT_ID();

INSERT INTO quartos(codigo,capacidade,ativo) VALUES ('A-08',4,1);

SET @demo_quarto_8 = LAST_INSERT_ID();

INSERT INTO quartos(codigo,capacidade,ativo) VALUES ('A-09',4,1);

SET @demo_quarto_9 = LAST_INSERT_ID();

INSERT INTO quartos(codigo,capacidade,ativo) VALUES ('A-10',4,1);

SET @demo_quarto_10 = LAST_INSERT_ID();

INSERT INTO quartos(codigo,capacidade,ativo) VALUES ('A-11',4,1);

SET @demo_quarto_11 = LAST_INSERT_ID();

INSERT INTO quartos(codigo,capacidade,ativo) VALUES ('A-12',4,1);

SET @demo_quarto_12 = LAST_INSERT_ID();

INSERT INTO quartos(codigo,capacidade,ativo) VALUES ('A-13',4,1);

SET @demo_quarto_13 = LAST_INSERT_ID();

INSERT INTO quartos(codigo,capacidade,ativo) VALUES ('A-14',4,1);

SET @demo_quarto_14 = LAST_INSERT_ID();

INSERT INTO quartos(codigo,capacidade,ativo) VALUES ('B-15',4,1);

SET @demo_quarto_15 = LAST_INSERT_ID();

INSERT INTO quartos(codigo,capacidade,ativo) VALUES ('B-16',4,1);

SET @demo_quarto_16 = LAST_INSERT_ID();

INSERT INTO quartos(codigo,capacidade,ativo) VALUES ('B-17',4,1);

SET @demo_quarto_17 = LAST_INSERT_ID();

INSERT INTO quartos(codigo,capacidade,ativo) VALUES ('B-18',4,1);

SET @demo_quarto_18 = LAST_INSERT_ID();

INSERT INTO quartos(codigo,capacidade,ativo) VALUES ('B-19',4,1);

SET @demo_quarto_19 = LAST_INSERT_ID();

INSERT INTO quartos(codigo,capacidade,ativo) VALUES ('B-20',4,1);

SET @demo_quarto_20 = LAST_INSERT_ID();

INSERT INTO quartos(codigo,capacidade,ativo) VALUES ('B-21',4,1);

SET @demo_quarto_21 = LAST_INSERT_ID();

INSERT INTO quartos(codigo,capacidade,ativo) VALUES ('B-22',4,1);

SET @demo_quarto_22 = LAST_INSERT_ID();

INSERT INTO quartos(codigo,capacidade,ativo) VALUES ('B-23',4,1);

SET @demo_quarto_23 = LAST_INSERT_ID();

INSERT INTO quartos(codigo,capacidade,ativo) VALUES ('B-24',4,1);

SET @demo_quarto_24 = LAST_INSERT_ID();

INSERT INTO quartos(codigo,capacidade,ativo) VALUES ('B-25',4,1);

SET @demo_quarto_25 = LAST_INSERT_ID();

INSERT INTO quartos(codigo,capacidade,ativo) VALUES ('B-26',4,1);

SET @demo_quarto_26 = LAST_INSERT_ID();

INSERT INTO quartos(codigo,capacidade,ativo) VALUES ('B-27',4,1);

SET @demo_quarto_27 = LAST_INSERT_ID();

INSERT INTO quartos(codigo,capacidade,ativo) VALUES ('B-28',4,0);

SET @demo_quarto_28 = LAST_INSERT_ID();

INSERT INTO idosos(nome,cpf,identificador,quarto_id,ativo,criado_em) VALUES ('Maria Oliveira','70000000159','DEMO-001',@demo_quarto_1,1,DATE_SUB(NOW(),INTERVAL 179 DAY));

SET @demo_idoso_1 = LAST_INSERT_ID();

INSERT INTO familiares(usuario_id,nome,cpf,telefone,email,parentesco_principal) VALUES (@demo_familiar,'Ana Oliveira','80000000167','11900000001','familiar@asilosoft.local','Filha');

INSERT INTO idosos_familiares(idoso_id,familiar_id,parentesco,responsavel_principal) VALUES (@demo_idoso_1,LAST_INSERT_ID(),'Filha',1);

INSERT INTO familiares(usuario_id,nome,cpf,telefone,email,parentesco_principal) VALUES (NULL,'Rafael Oliveira','80000010120','11900000101','contato101@example.com','Neto');

INSERT INTO idosos_familiares(idoso_id,familiar_id,parentesco,responsavel_principal) VALUES (@demo_idoso_1,LAST_INSERT_ID(),'Neto',0);

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_1,@demo_saude,'[Demonstração] Participou da atividade de música e convivência. Boa aceitação das refeições.',1,DATE_SUB(NOW(),INTERVAL 16 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_1,@demo_saude,'[Demonstração] Avaliação da equipe: manter acompanhamento e rotina de cuidados individualizada.',0,DATE_SUB(NOW(),INTERVAL 8 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_1,@demo_saude,'[Demonstração] Rotina tranquila, participou da caminhada assistida e recebeu contato da família.',1,DATE_SUB(NOW(),INTERVAL 2 DAY));

INSERT INTO idosos(nome,cpf,identificador,quarto_id,ativo,criado_em) VALUES ('José Oliveira','70000000230','DEMO-002',@demo_quarto_1,1,DATE_SUB(NOW(),INTERVAL 178 DAY));

SET @demo_idoso_2 = LAST_INSERT_ID();

INSERT INTO familiares(usuario_id,nome,cpf,telefone,email,parentesco_principal) VALUES (NULL,'Beatriz Oliveira','80000000248','11900000002','contato2@example.com','Filha');

INSERT INTO idosos_familiares(idoso_id,familiar_id,parentesco,responsavel_principal) VALUES (@demo_idoso_2,LAST_INSERT_ID(),'Filha',1);

INSERT INTO familiares(usuario_id,nome,cpf,telefone,email,parentesco_principal) VALUES (NULL,'Beatriz Oliveira','80000010200','11900000102','contato102@example.com','Neto');

INSERT INTO idosos_familiares(idoso_id,familiar_id,parentesco,responsavel_principal) VALUES (@demo_idoso_2,LAST_INSERT_ID(),'Neto',0);

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_2,@demo_saude,'[Demonstração] Participou da atividade de música e convivência. Boa aceitação das refeições.',1,DATE_SUB(NOW(),INTERVAL 17 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_2,@demo_saude,'[Demonstração] Avaliação da equipe: manter acompanhamento e rotina de cuidados individualizada.',0,DATE_SUB(NOW(),INTERVAL 9 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_2,@demo_saude,'[Demonstração] Rotina tranquila, participou da caminhada assistida e recebeu contato da família.',1,DATE_SUB(NOW(),INTERVAL 3 DAY));

INSERT INTO idosos(nome,cpf,identificador,quarto_id,ativo,criado_em) VALUES ('Ana Oliveira','70000000310','DEMO-003',@demo_quarto_1,1,DATE_SUB(NOW(),INTERVAL 177 DAY));

SET @demo_idoso_3 = LAST_INSERT_ID();

INSERT INTO familiares(usuario_id,nome,cpf,telefone,email,parentesco_principal) VALUES (NULL,'Gabriel Oliveira','80000000329','11900000003','contato3@example.com','Filha');

INSERT INTO idosos_familiares(idoso_id,familiar_id,parentesco,responsavel_principal) VALUES (@demo_idoso_3,LAST_INSERT_ID(),'Filha',1);

INSERT INTO familiares(usuario_id,nome,cpf,telefone,email,parentesco_principal) VALUES (NULL,'Gabriel Oliveira','80000010391','11900000103','contato103@example.com','Neto');

INSERT INTO idosos_familiares(idoso_id,familiar_id,parentesco,responsavel_principal) VALUES (@demo_idoso_3,LAST_INSERT_ID(),'Neto',0);

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_3,@demo_saude,'[Demonstração] Participou da atividade de música e convivência. Boa aceitação das refeições.',1,DATE_SUB(NOW(),INTERVAL 15 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_3,@demo_saude,'[Demonstração] Avaliação da equipe: manter acompanhamento e rotina de cuidados individualizada.',0,DATE_SUB(NOW(),INTERVAL 7 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_3,@demo_saude,'[Demonstração] Rotina tranquila, participou da caminhada assistida e recebeu contato da família.',1,DATE_SUB(NOW(),INTERVAL 1 DAY));

INSERT INTO idosos(nome,cpf,identificador,quarto_id,ativo,criado_em) VALUES ('João Oliveira','70000000400','DEMO-004',@demo_quarto_1,1,DATE_SUB(NOW(),INTERVAL 176 DAY));

SET @demo_idoso_4 = LAST_INSERT_ID();

INSERT INTO familiares(usuario_id,nome,cpf,telefone,email,parentesco_principal) VALUES (NULL,'Carolina Oliveira','80000000400','11900000004','contato4@example.com','Filha');

INSERT INTO idosos_familiares(idoso_id,familiar_id,parentesco,responsavel_principal) VALUES (@demo_idoso_4,LAST_INSERT_ID(),'Filha',1);

INSERT INTO familiares(usuario_id,nome,cpf,telefone,email,parentesco_principal) VALUES (NULL,'Carolina Oliveira','80000010472','11900000104','contato104@example.com','Neto');

INSERT INTO idosos_familiares(idoso_id,familiar_id,parentesco,responsavel_principal) VALUES (@demo_idoso_4,LAST_INSERT_ID(),'Neto',0);

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_4,@demo_saude,'[Demonstração] Participou da atividade de música e convivência. Boa aceitação das refeições.',1,DATE_SUB(NOW(),INTERVAL 16 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_4,@demo_saude,'[Demonstração] Avaliação da equipe: manter acompanhamento e rotina de cuidados individualizada.',0,DATE_SUB(NOW(),INTERVAL 8 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_4,@demo_saude,'[Demonstração] Rotina tranquila, participou da caminhada assistida e recebeu contato da família.',1,DATE_SUB(NOW(),INTERVAL 2 DAY));

INSERT INTO idosos(nome,cpf,identificador,quarto_id,ativo,criado_em) VALUES ('Francisca Oliveira','70000000582','DEMO-005',@demo_quarto_2,1,DATE_SUB(NOW(),INTERVAL 175 DAY));

SET @demo_idoso_5 = LAST_INSERT_ID();

INSERT INTO familiares(usuario_id,nome,cpf,telefone,email,parentesco_principal) VALUES (NULL,'Rafael Oliveira','80000000590','11900000005','contato5@example.com','Filha');

INSERT INTO idosos_familiares(idoso_id,familiar_id,parentesco,responsavel_principal) VALUES (@demo_idoso_5,LAST_INSERT_ID(),'Filha',1);

INSERT INTO familiares(usuario_id,nome,cpf,telefone,email,parentesco_principal) VALUES (NULL,'Rafael Oliveira','80000010553','11900000105','contato105@example.com','Neto');

INSERT INTO idosos_familiares(idoso_id,familiar_id,parentesco,responsavel_principal) VALUES (@demo_idoso_5,LAST_INSERT_ID(),'Neto',0);

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_5,@demo_saude,'[Demonstração] Participou da atividade de música e convivência. Boa aceitação das refeições.',1,DATE_SUB(NOW(),INTERVAL 17 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_5,@demo_saude,'[Demonstração] Avaliação da equipe: manter acompanhamento e rotina de cuidados individualizada.',0,DATE_SUB(NOW(),INTERVAL 9 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_5,@demo_saude,'[Demonstração] Rotina tranquila, participou da caminhada assistida e recebeu contato da família.',1,DATE_SUB(NOW(),INTERVAL 3 DAY));

INSERT INTO idosos(nome,cpf,identificador,quarto_id,ativo,criado_em) VALUES ('Antônio Oliveira','70000000663','DEMO-006',@demo_quarto_2,1,DATE_SUB(NOW(),INTERVAL 174 DAY));

SET @demo_idoso_6 = LAST_INSERT_ID();

INSERT INTO familiares(usuario_id,nome,cpf,telefone,email,parentesco_principal) VALUES (NULL,'Beatriz Oliveira','80000000671','11900000006','contato6@example.com','Filha');

INSERT INTO idosos_familiares(idoso_id,familiar_id,parentesco,responsavel_principal) VALUES (@demo_idoso_6,LAST_INSERT_ID(),'Filha',1);

INSERT INTO familiares(usuario_id,nome,cpf,telefone,email,parentesco_principal) VALUES (NULL,'Beatriz Oliveira','80000010634','11900000106','contato106@example.com','Neto');

INSERT INTO idosos_familiares(idoso_id,familiar_id,parentesco,responsavel_principal) VALUES (@demo_idoso_6,LAST_INSERT_ID(),'Neto',0);

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_6,@demo_saude,'[Demonstração] Participou da atividade de música e convivência. Boa aceitação das refeições.',1,DATE_SUB(NOW(),INTERVAL 15 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_6,@demo_saude,'[Demonstração] Avaliação da equipe: manter acompanhamento e rotina de cuidados individualizada.',0,DATE_SUB(NOW(),INTERVAL 7 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_6,@demo_saude,'[Demonstração] Rotina tranquila, participou da caminhada assistida e recebeu contato da família.',1,DATE_SUB(NOW(),INTERVAL 1 DAY));

INSERT INTO idosos(nome,cpf,identificador,quarto_id,ativo,criado_em) VALUES ('Helena Oliveira','70000000744','DEMO-007',@demo_quarto_2,1,DATE_SUB(NOW(),INTERVAL 173 DAY));

SET @demo_idoso_7 = LAST_INSERT_ID();

INSERT INTO familiares(usuario_id,nome,cpf,telefone,email,parentesco_principal) VALUES (NULL,'Gabriel Oliveira','80000000752','11900000007','contato7@example.com','Filha');

INSERT INTO idosos_familiares(idoso_id,familiar_id,parentesco,responsavel_principal) VALUES (@demo_idoso_7,LAST_INSERT_ID(),'Filha',1);

INSERT INTO familiares(usuario_id,nome,cpf,telefone,email,parentesco_principal) VALUES (NULL,'Gabriel Oliveira','80000010715','11900000107','contato107@example.com','Neto');

INSERT INTO idosos_familiares(idoso_id,familiar_id,parentesco,responsavel_principal) VALUES (@demo_idoso_7,LAST_INSERT_ID(),'Neto',0);

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_7,@demo_saude,'[Demonstração] Participou da atividade de música e convivência. Boa aceitação das refeições.',1,DATE_SUB(NOW(),INTERVAL 16 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_7,@demo_saude,'[Demonstração] Avaliação da equipe: manter acompanhamento e rotina de cuidados individualizada.',0,DATE_SUB(NOW(),INTERVAL 8 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_7,@demo_saude,'[Demonstração] Rotina tranquila, participou da caminhada assistida e recebeu contato da família.',1,DATE_SUB(NOW(),INTERVAL 2 DAY));

INSERT INTO idosos(nome,cpf,identificador,quarto_id,ativo,criado_em) VALUES ('Manuel Oliveira','70000000825','DEMO-008',@demo_quarto_2,1,DATE_SUB(NOW(),INTERVAL 172 DAY));

SET @demo_idoso_8 = LAST_INSERT_ID();

INSERT INTO familiares(usuario_id,nome,cpf,telefone,email,parentesco_principal) VALUES (NULL,'Carolina Oliveira','80000000833','11900000008','contato8@example.com','Filha');

INSERT INTO idosos_familiares(idoso_id,familiar_id,parentesco,responsavel_principal) VALUES (@demo_idoso_8,LAST_INSERT_ID(),'Filha',1);

INSERT INTO familiares(usuario_id,nome,cpf,telefone,email,parentesco_principal) VALUES (NULL,'Carolina Oliveira','80000010804','11900000108','contato108@example.com','Neto');

INSERT INTO idosos_familiares(idoso_id,familiar_id,parentesco,responsavel_principal) VALUES (@demo_idoso_8,LAST_INSERT_ID(),'Neto',0);

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_8,@demo_saude,'[Demonstração] Participou da atividade de música e convivência. Boa aceitação das refeições.',1,DATE_SUB(NOW(),INTERVAL 17 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_8,@demo_saude,'[Demonstração] Avaliação da equipe: manter acompanhamento e rotina de cuidados individualizada.',0,DATE_SUB(NOW(),INTERVAL 9 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_8,@demo_saude,'[Demonstração] Rotina tranquila, participou da caminhada assistida e recebeu contato da família.',1,DATE_SUB(NOW(),INTERVAL 3 DAY));

INSERT INTO idosos(nome,cpf,identificador,quarto_id,ativo,criado_em) VALUES ('Lúcia Oliveira','70000000906','DEMO-009',@demo_quarto_3,1,DATE_SUB(NOW(),INTERVAL 171 DAY));

SET @demo_idoso_9 = LAST_INSERT_ID();

INSERT INTO familiares(usuario_id,nome,cpf,telefone,email,parentesco_principal) VALUES (NULL,'Rafael Oliveira','80000000914','11900000009','contato9@example.com','Filha');

INSERT INTO idosos_familiares(idoso_id,familiar_id,parentesco,responsavel_principal) VALUES (@demo_idoso_9,LAST_INSERT_ID(),'Filha',1);

INSERT INTO familiares(usuario_id,nome,cpf,telefone,email,parentesco_principal) VALUES (NULL,'Rafael Oliveira','80000010987','11900000109','contato109@example.com','Neto');

INSERT INTO idosos_familiares(idoso_id,familiar_id,parentesco,responsavel_principal) VALUES (@demo_idoso_9,LAST_INSERT_ID(),'Neto',0);

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_9,@demo_saude,'[Demonstração] Participou da atividade de música e convivência. Boa aceitação das refeições.',1,DATE_SUB(NOW(),INTERVAL 15 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_9,@demo_saude,'[Demonstração] Avaliação da equipe: manter acompanhamento e rotina de cuidados individualizada.',0,DATE_SUB(NOW(),INTERVAL 7 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_9,@demo_saude,'[Demonstração] Rotina tranquila, participou da caminhada assistida e recebeu contato da família.',1,DATE_SUB(NOW(),INTERVAL 1 DAY));

INSERT INTO idosos(nome,cpf,identificador,quarto_id,ativo,criado_em) VALUES ('Pedro Oliveira','70000001040','DEMO-010',@demo_quarto_3,1,DATE_SUB(NOW(),INTERVAL 170 DAY));

SET @demo_idoso_10 = LAST_INSERT_ID();

INSERT INTO familiares(usuario_id,nome,cpf,telefone,email,parentesco_principal) VALUES (NULL,'Beatriz Oliveira','80000001058','11900000010','contato10@example.com','Filha');

INSERT INTO idosos_familiares(idoso_id,familiar_id,parentesco,responsavel_principal) VALUES (@demo_idoso_10,LAST_INSERT_ID(),'Filha',1);

INSERT INTO familiares(usuario_id,nome,cpf,telefone,email,parentesco_principal) VALUES (NULL,'Beatriz Oliveira','80000011010','11900000110','contato110@example.com','Neto');

INSERT INTO idosos_familiares(idoso_id,familiar_id,parentesco,responsavel_principal) VALUES (@demo_idoso_10,LAST_INSERT_ID(),'Neto',0);

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_10,@demo_saude,'[Demonstração] Participou da atividade de música e convivência. Boa aceitação das refeições.',1,DATE_SUB(NOW(),INTERVAL 16 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_10,@demo_saude,'[Demonstração] Avaliação da equipe: manter acompanhamento e rotina de cuidados individualizada.',0,DATE_SUB(NOW(),INTERVAL 8 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_10,@demo_saude,'[Demonstração] Rotina tranquila, participou da caminhada assistida e recebeu contato da família.',1,DATE_SUB(NOW(),INTERVAL 2 DAY));

INSERT INTO idosos(nome,cpf,identificador,quarto_id,ativo,criado_em) VALUES ('Tereza Oliveira','70000001120','DEMO-011',@demo_quarto_3,1,DATE_SUB(NOW(),INTERVAL 169 DAY));

SET @demo_idoso_11 = LAST_INSERT_ID();

INSERT INTO familiares(usuario_id,nome,cpf,telefone,email,parentesco_principal) VALUES (NULL,'Gabriel Oliveira','80000001139','11900000011','contato11@example.com','Filha');

INSERT INTO idosos_familiares(idoso_id,familiar_id,parentesco,responsavel_principal) VALUES (@demo_idoso_11,LAST_INSERT_ID(),'Filha',1);

INSERT INTO familiares(usuario_id,nome,cpf,telefone,email,parentesco_principal) VALUES (NULL,'Gabriel Oliveira','80000011100','11900000111','contato111@example.com','Neto');

INSERT INTO idosos_familiares(idoso_id,familiar_id,parentesco,responsavel_principal) VALUES (@demo_idoso_11,LAST_INSERT_ID(),'Neto',0);

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_11,@demo_saude,'[Demonstração] Participou da atividade de música e convivência. Boa aceitação das refeições.',1,DATE_SUB(NOW(),INTERVAL 17 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_11,@demo_saude,'[Demonstração] Avaliação da equipe: manter acompanhamento e rotina de cuidados individualizada.',0,DATE_SUB(NOW(),INTERVAL 9 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_11,@demo_saude,'[Demonstração] Rotina tranquila, participou da caminhada assistida e recebeu contato da família.',1,DATE_SUB(NOW(),INTERVAL 3 DAY));

INSERT INTO idosos(nome,cpf,identificador,quarto_id,ativo,criado_em) VALUES ('Luiz Oliveira','70000001201','DEMO-012',@demo_quarto_3,1,DATE_SUB(NOW(),INTERVAL 168 DAY));

SET @demo_idoso_12 = LAST_INSERT_ID();

INSERT INTO familiares(usuario_id,nome,cpf,telefone,email,parentesco_principal) VALUES (NULL,'Carolina Oliveira','80000001210','11900000012','contato12@example.com','Filha');

INSERT INTO idosos_familiares(idoso_id,familiar_id,parentesco,responsavel_principal) VALUES (@demo_idoso_12,LAST_INSERT_ID(),'Filha',1);

INSERT INTO familiares(usuario_id,nome,cpf,telefone,email,parentesco_principal) VALUES (NULL,'Carolina Oliveira','80000011282','11900000112','contato112@example.com','Neto');

INSERT INTO idosos_familiares(idoso_id,familiar_id,parentesco,responsavel_principal) VALUES (@demo_idoso_12,LAST_INSERT_ID(),'Neto',0);

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_12,@demo_saude,'[Demonstração] Participou da atividade de música e convivência. Boa aceitação das refeições.',1,DATE_SUB(NOW(),INTERVAL 15 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_12,@demo_saude,'[Demonstração] Avaliação da equipe: manter acompanhamento e rotina de cuidados individualizada.',0,DATE_SUB(NOW(),INTERVAL 7 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_12,@demo_saude,'[Demonstração] Rotina tranquila, participou da caminhada assistida e recebeu contato da família.',1,DATE_SUB(NOW(),INTERVAL 1 DAY));

INSERT INTO idosos(nome,cpf,identificador,quarto_id,ativo,criado_em) VALUES ('Rosa Oliveira','70000001392','DEMO-013',@demo_quarto_4,1,DATE_SUB(NOW(),INTERVAL 167 DAY));

SET @demo_idoso_13 = LAST_INSERT_ID();

INSERT INTO familiares(usuario_id,nome,cpf,telefone,email,parentesco_principal) VALUES (NULL,'Rafael Oliveira','80000001309','11900000013','contato13@example.com','Filha');

INSERT INTO idosos_familiares(idoso_id,familiar_id,parentesco,responsavel_principal) VALUES (@demo_idoso_13,LAST_INSERT_ID(),'Filha',1);

INSERT INTO familiares(usuario_id,nome,cpf,telefone,email,parentesco_principal) VALUES (NULL,'Rafael Oliveira','80000011363','11900000113','contato113@example.com','Neto');

INSERT INTO idosos_familiares(idoso_id,familiar_id,parentesco,responsavel_principal) VALUES (@demo_idoso_13,LAST_INSERT_ID(),'Neto',0);

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_13,@demo_saude,'[Demonstração] Participou da atividade de música e convivência. Boa aceitação das refeições.',1,DATE_SUB(NOW(),INTERVAL 16 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_13,@demo_saude,'[Demonstração] Avaliação da equipe: manter acompanhamento e rotina de cuidados individualizada.',0,DATE_SUB(NOW(),INTERVAL 8 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_13,@demo_saude,'[Demonstração] Rotina tranquila, participou da caminhada assistida e recebeu contato da família.',1,DATE_SUB(NOW(),INTERVAL 2 DAY));

INSERT INTO idosos(nome,cpf,identificador,quarto_id,ativo,criado_em) VALUES ('Paulo Oliveira','70000001473','DEMO-014',@demo_quarto_4,1,DATE_SUB(NOW(),INTERVAL 166 DAY));

SET @demo_idoso_14 = LAST_INSERT_ID();

INSERT INTO familiares(usuario_id,nome,cpf,telefone,email,parentesco_principal) VALUES (NULL,'Beatriz Oliveira','80000001481','11900000014','contato14@example.com','Filha');

INSERT INTO idosos_familiares(idoso_id,familiar_id,parentesco,responsavel_principal) VALUES (@demo_idoso_14,LAST_INSERT_ID(),'Filha',1);

INSERT INTO familiares(usuario_id,nome,cpf,telefone,email,parentesco_principal) VALUES (NULL,'Beatriz Oliveira','80000011444','11900000114','contato114@example.com','Neto');

INSERT INTO idosos_familiares(idoso_id,familiar_id,parentesco,responsavel_principal) VALUES (@demo_idoso_14,LAST_INSERT_ID(),'Neto',0);

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_14,@demo_saude,'[Demonstração] Participou da atividade de música e convivência. Boa aceitação das refeições.',1,DATE_SUB(NOW(),INTERVAL 17 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_14,@demo_saude,'[Demonstração] Avaliação da equipe: manter acompanhamento e rotina de cuidados individualizada.',0,DATE_SUB(NOW(),INTERVAL 9 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_14,@demo_saude,'[Demonstração] Rotina tranquila, participou da caminhada assistida e recebeu contato da família.',1,DATE_SUB(NOW(),INTERVAL 3 DAY));

INSERT INTO idosos(nome,cpf,identificador,quarto_id,ativo,criado_em) VALUES ('Célia Oliveira','70000001554','DEMO-015',@demo_quarto_4,1,DATE_SUB(NOW(),INTERVAL 165 DAY));

SET @demo_idoso_15 = LAST_INSERT_ID();

INSERT INTO familiares(usuario_id,nome,cpf,telefone,email,parentesco_principal) VALUES (NULL,'Gabriel Oliveira','80000001562','11900000015','contato15@example.com','Filha');

INSERT INTO idosos_familiares(idoso_id,familiar_id,parentesco,responsavel_principal) VALUES (@demo_idoso_15,LAST_INSERT_ID(),'Filha',1);

INSERT INTO familiares(usuario_id,nome,cpf,telefone,email,parentesco_principal) VALUES (NULL,'Gabriel Oliveira','80000011525','11900000115','contato115@example.com','Neto');

INSERT INTO idosos_familiares(idoso_id,familiar_id,parentesco,responsavel_principal) VALUES (@demo_idoso_15,LAST_INSERT_ID(),'Neto',0);

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_15,@demo_saude,'[Demonstração] Participou da atividade de música e convivência. Boa aceitação das refeições.',1,DATE_SUB(NOW(),INTERVAL 15 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_15,@demo_saude,'[Demonstração] Avaliação da equipe: manter acompanhamento e rotina de cuidados individualizada.',0,DATE_SUB(NOW(),INTERVAL 7 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_15,@demo_saude,'[Demonstração] Rotina tranquila, participou da caminhada assistida e recebeu contato da família.',1,DATE_SUB(NOW(),INTERVAL 1 DAY));

INSERT INTO idosos(nome,cpf,identificador,quarto_id,ativo,criado_em) VALUES ('Joaquim Oliveira','70000001635','DEMO-016',@demo_quarto_4,1,DATE_SUB(NOW(),INTERVAL 164 DAY));

SET @demo_idoso_16 = LAST_INSERT_ID();

INSERT INTO familiares(usuario_id,nome,cpf,telefone,email,parentesco_principal) VALUES (NULL,'Carolina Oliveira','80000001643','11900000016','contato16@example.com','Filha');

INSERT INTO idosos_familiares(idoso_id,familiar_id,parentesco,responsavel_principal) VALUES (@demo_idoso_16,LAST_INSERT_ID(),'Filha',1);

INSERT INTO familiares(usuario_id,nome,cpf,telefone,email,parentesco_principal) VALUES (NULL,'Carolina Oliveira','80000011606','11900000116','contato116@example.com','Neto');

INSERT INTO idosos_familiares(idoso_id,familiar_id,parentesco,responsavel_principal) VALUES (@demo_idoso_16,LAST_INSERT_ID(),'Neto',0);

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_16,@demo_saude,'[Demonstração] Participou da atividade de música e convivência. Boa aceitação das refeições.',1,DATE_SUB(NOW(),INTERVAL 16 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_16,@demo_saude,'[Demonstração] Avaliação da equipe: manter acompanhamento e rotina de cuidados individualizada.',0,DATE_SUB(NOW(),INTERVAL 8 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_16,@demo_saude,'[Demonstração] Rotina tranquila, participou da caminhada assistida e recebeu contato da família.',1,DATE_SUB(NOW(),INTERVAL 2 DAY));

INSERT INTO idosos(nome,cpf,identificador,quarto_id,ativo,criado_em) VALUES ('Rita Oliveira','70000001716','DEMO-017',@demo_quarto_5,1,DATE_SUB(NOW(),INTERVAL 163 DAY));

SET @demo_idoso_17 = LAST_INSERT_ID();

INSERT INTO familiares(usuario_id,nome,cpf,telefone,email,parentesco_principal) VALUES (NULL,'Rafael Oliveira','80000001724','11900000017','contato17@example.com','Filha');

INSERT INTO idosos_familiares(idoso_id,familiar_id,parentesco,responsavel_principal) VALUES (@demo_idoso_17,LAST_INSERT_ID(),'Filha',1);

INSERT INTO familiares(usuario_id,nome,cpf,telefone,email,parentesco_principal) VALUES (NULL,'Rafael Oliveira','80000011797','11900000117','contato117@example.com','Neto');

INSERT INTO idosos_familiares(idoso_id,familiar_id,parentesco,responsavel_principal) VALUES (@demo_idoso_17,LAST_INSERT_ID(),'Neto',0);

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_17,@demo_saude,'[Demonstração] Participou da atividade de música e convivência. Boa aceitação das refeições.',1,DATE_SUB(NOW(),INTERVAL 17 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_17,@demo_saude,'[Demonstração] Avaliação da equipe: manter acompanhamento e rotina de cuidados individualizada.',0,DATE_SUB(NOW(),INTERVAL 9 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_17,@demo_saude,'[Demonstração] Rotina tranquila, participou da caminhada assistida e recebeu contato da família.',1,DATE_SUB(NOW(),INTERVAL 3 DAY));

INSERT INTO idosos(nome,cpf,identificador,quarto_id,ativo,criado_em) VALUES ('Sebastião Oliveira','70000001805','DEMO-018',@demo_quarto_5,1,DATE_SUB(NOW(),INTERVAL 162 DAY));

SET @demo_idoso_18 = LAST_INSERT_ID();

INSERT INTO familiares(usuario_id,nome,cpf,telefone,email,parentesco_principal) VALUES (NULL,'Beatriz Oliveira','80000001805','11900000018','contato18@example.com','Filha');

INSERT INTO idosos_familiares(idoso_id,familiar_id,parentesco,responsavel_principal) VALUES (@demo_idoso_18,LAST_INSERT_ID(),'Filha',1);

INSERT INTO familiares(usuario_id,nome,cpf,telefone,email,parentesco_principal) VALUES (NULL,'Beatriz Oliveira','80000011878','11900000118','contato118@example.com','Neto');

INSERT INTO idosos_familiares(idoso_id,familiar_id,parentesco,responsavel_principal) VALUES (@demo_idoso_18,LAST_INSERT_ID(),'Neto',0);

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_18,@demo_saude,'[Demonstração] Participou da atividade de música e convivência. Boa aceitação das refeições.',1,DATE_SUB(NOW(),INTERVAL 15 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_18,@demo_saude,'[Demonstração] Avaliação da equipe: manter acompanhamento e rotina de cuidados individualizada.',0,DATE_SUB(NOW(),INTERVAL 7 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_18,@demo_saude,'[Demonstração] Rotina tranquila, participou da caminhada assistida e recebeu contato da família.',1,DATE_SUB(NOW(),INTERVAL 1 DAY));

INSERT INTO idosos(nome,cpf,identificador,quarto_id,ativo,criado_em) VALUES ('Alice Oliveira','70000001988','DEMO-019',@demo_quarto_5,1,DATE_SUB(NOW(),INTERVAL 161 DAY));

SET @demo_idoso_19 = LAST_INSERT_ID();

INSERT INTO familiares(usuario_id,nome,cpf,telefone,email,parentesco_principal) VALUES (NULL,'Gabriel Oliveira','80000001996','11900000019','contato19@example.com','Filha');

INSERT INTO idosos_familiares(idoso_id,familiar_id,parentesco,responsavel_principal) VALUES (@demo_idoso_19,LAST_INSERT_ID(),'Filha',1);

INSERT INTO familiares(usuario_id,nome,cpf,telefone,email,parentesco_principal) VALUES (NULL,'Gabriel Oliveira','80000011959','11900000119','contato119@example.com','Neto');

INSERT INTO idosos_familiares(idoso_id,familiar_id,parentesco,responsavel_principal) VALUES (@demo_idoso_19,LAST_INSERT_ID(),'Neto',0);

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_19,@demo_saude,'[Demonstração] Participou da atividade de música e convivência. Boa aceitação das refeições.',1,DATE_SUB(NOW(),INTERVAL 16 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_19,@demo_saude,'[Demonstração] Avaliação da equipe: manter acompanhamento e rotina de cuidados individualizada.',0,DATE_SUB(NOW(),INTERVAL 8 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_19,@demo_saude,'[Demonstração] Rotina tranquila, participou da caminhada assistida e recebeu contato da família.',1,DATE_SUB(NOW(),INTERVAL 2 DAY));

INSERT INTO idosos(nome,cpf,identificador,quarto_id,ativo,criado_em) VALUES ('Francisco Oliveira','70000002011','DEMO-020',@demo_quarto_5,1,DATE_SUB(NOW(),INTERVAL 160 DAY));

SET @demo_idoso_20 = LAST_INSERT_ID();

INSERT INTO familiares(usuario_id,nome,cpf,telefone,email,parentesco_principal) VALUES (NULL,'Carolina Oliveira','80000002020','11900000020','contato20@example.com','Filha');

INSERT INTO idosos_familiares(idoso_id,familiar_id,parentesco,responsavel_principal) VALUES (@demo_idoso_20,LAST_INSERT_ID(),'Filha',1);

INSERT INTO familiares(usuario_id,nome,cpf,telefone,email,parentesco_principal) VALUES (NULL,'Carolina Oliveira','80000012092','11900000120','contato120@example.com','Neto');

INSERT INTO idosos_familiares(idoso_id,familiar_id,parentesco,responsavel_principal) VALUES (@demo_idoso_20,LAST_INSERT_ID(),'Neto',0);

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_20,@demo_saude,'[Demonstração] Participou da atividade de música e convivência. Boa aceitação das refeições.',1,DATE_SUB(NOW(),INTERVAL 17 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_20,@demo_saude,'[Demonstração] Avaliação da equipe: manter acompanhamento e rotina de cuidados individualizada.',0,DATE_SUB(NOW(),INTERVAL 9 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_20,@demo_saude,'[Demonstração] Rotina tranquila, participou da caminhada assistida e recebeu contato da família.',1,DATE_SUB(NOW(),INTERVAL 3 DAY));

INSERT INTO idosos(nome,cpf,identificador,quarto_id,ativo,criado_em) VALUES ('Maria Santos','70000002100','DEMO-021',@demo_quarto_6,1,DATE_SUB(NOW(),INTERVAL 159 DAY));

SET @demo_idoso_21 = LAST_INSERT_ID();

INSERT INTO familiares(usuario_id,nome,cpf,telefone,email,parentesco_principal) VALUES (NULL,'Rafael Santos','80000002100','11900000021','contato21@example.com','Filha');

INSERT INTO idosos_familiares(idoso_id,familiar_id,parentesco,responsavel_principal) VALUES (@demo_idoso_21,LAST_INSERT_ID(),'Filha',1);

INSERT INTO familiares(usuario_id,nome,cpf,telefone,email,parentesco_principal) VALUES (NULL,'Rafael Santos','80000012173','11900000121','contato121@example.com','Neto');

INSERT INTO idosos_familiares(idoso_id,familiar_id,parentesco,responsavel_principal) VALUES (@demo_idoso_21,LAST_INSERT_ID(),'Neto',0);

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_21,@demo_saude,'[Demonstração] Participou da atividade de música e convivência. Boa aceitação das refeições.',1,DATE_SUB(NOW(),INTERVAL 15 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_21,@demo_saude,'[Demonstração] Avaliação da equipe: manter acompanhamento e rotina de cuidados individualizada.',0,DATE_SUB(NOW(),INTERVAL 7 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_21,@demo_saude,'[Demonstração] Rotina tranquila, participou da caminhada assistida e recebeu contato da família.',1,DATE_SUB(NOW(),INTERVAL 1 DAY));

INSERT INTO idosos(nome,cpf,identificador,quarto_id,ativo,criado_em) VALUES ('José Santos','70000002283','DEMO-022',@demo_quarto_6,1,DATE_SUB(NOW(),INTERVAL 158 DAY));

SET @demo_idoso_22 = LAST_INSERT_ID();

INSERT INTO familiares(usuario_id,nome,cpf,telefone,email,parentesco_principal) VALUES (NULL,'Beatriz Santos','80000002291','11900000022','contato22@example.com','Filha');

INSERT INTO idosos_familiares(idoso_id,familiar_id,parentesco,responsavel_principal) VALUES (@demo_idoso_22,LAST_INSERT_ID(),'Filha',1);

INSERT INTO familiares(usuario_id,nome,cpf,telefone,email,parentesco_principal) VALUES (NULL,'Beatriz Santos','80000012254','11900000122','contato122@example.com','Neto');

INSERT INTO idosos_familiares(idoso_id,familiar_id,parentesco,responsavel_principal) VALUES (@demo_idoso_22,LAST_INSERT_ID(),'Neto',0);

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_22,@demo_saude,'[Demonstração] Participou da atividade de música e convivência. Boa aceitação das refeições.',1,DATE_SUB(NOW(),INTERVAL 16 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_22,@demo_saude,'[Demonstração] Avaliação da equipe: manter acompanhamento e rotina de cuidados individualizada.',0,DATE_SUB(NOW(),INTERVAL 8 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_22,@demo_saude,'[Demonstração] Rotina tranquila, participou da caminhada assistida e recebeu contato da família.',1,DATE_SUB(NOW(),INTERVAL 2 DAY));

INSERT INTO idosos(nome,cpf,identificador,quarto_id,ativo,criado_em) VALUES ('Ana Santos','70000002364','DEMO-023',@demo_quarto_6,1,DATE_SUB(NOW(),INTERVAL 157 DAY));

SET @demo_idoso_23 = LAST_INSERT_ID();

INSERT INTO familiares(usuario_id,nome,cpf,telefone,email,parentesco_principal) VALUES (NULL,'Gabriel Santos','80000002372','11900000023','contato23@example.com','Filha');

INSERT INTO idosos_familiares(idoso_id,familiar_id,parentesco,responsavel_principal) VALUES (@demo_idoso_23,LAST_INSERT_ID(),'Filha',1);

INSERT INTO familiares(usuario_id,nome,cpf,telefone,email,parentesco_principal) VALUES (NULL,'Gabriel Santos','80000012335','11900000123','contato123@example.com','Neto');

INSERT INTO idosos_familiares(idoso_id,familiar_id,parentesco,responsavel_principal) VALUES (@demo_idoso_23,LAST_INSERT_ID(),'Neto',0);

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_23,@demo_saude,'[Demonstração] Participou da atividade de música e convivência. Boa aceitação das refeições.',1,DATE_SUB(NOW(),INTERVAL 17 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_23,@demo_saude,'[Demonstração] Avaliação da equipe: manter acompanhamento e rotina de cuidados individualizada.',0,DATE_SUB(NOW(),INTERVAL 9 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_23,@demo_saude,'[Demonstração] Rotina tranquila, participou da caminhada assistida e recebeu contato da família.',1,DATE_SUB(NOW(),INTERVAL 3 DAY));

INSERT INTO idosos(nome,cpf,identificador,quarto_id,ativo,criado_em) VALUES ('João Santos','70000002445','DEMO-024',@demo_quarto_6,1,DATE_SUB(NOW(),INTERVAL 156 DAY));

SET @demo_idoso_24 = LAST_INSERT_ID();

INSERT INTO familiares(usuario_id,nome,cpf,telefone,email,parentesco_principal) VALUES (NULL,'Carolina Santos','80000002453','11900000024','contato24@example.com','Filha');

INSERT INTO idosos_familiares(idoso_id,familiar_id,parentesco,responsavel_principal) VALUES (@demo_idoso_24,LAST_INSERT_ID(),'Filha',1);

INSERT INTO familiares(usuario_id,nome,cpf,telefone,email,parentesco_principal) VALUES (NULL,'Carolina Santos','80000012416','11900000124','contato124@example.com','Neto');

INSERT INTO idosos_familiares(idoso_id,familiar_id,parentesco,responsavel_principal) VALUES (@demo_idoso_24,LAST_INSERT_ID(),'Neto',0);

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_24,@demo_saude,'[Demonstração] Participou da atividade de música e convivência. Boa aceitação das refeições.',1,DATE_SUB(NOW(),INTERVAL 15 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_24,@demo_saude,'[Demonstração] Avaliação da equipe: manter acompanhamento e rotina de cuidados individualizada.',0,DATE_SUB(NOW(),INTERVAL 7 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_24,@demo_saude,'[Demonstração] Rotina tranquila, participou da caminhada assistida e recebeu contato da família.',1,DATE_SUB(NOW(),INTERVAL 1 DAY));

INSERT INTO idosos(nome,cpf,identificador,quarto_id,ativo,criado_em) VALUES ('Francisca Santos','70000002526','DEMO-025',@demo_quarto_7,1,DATE_SUB(NOW(),INTERVAL 155 DAY));

SET @demo_idoso_25 = LAST_INSERT_ID();

INSERT INTO familiares(usuario_id,nome,cpf,telefone,email,parentesco_principal) VALUES (NULL,'Rafael Santos','80000002534','11900000025','contato25@example.com','Filha');

INSERT INTO idosos_familiares(idoso_id,familiar_id,parentesco,responsavel_principal) VALUES (@demo_idoso_25,LAST_INSERT_ID(),'Filha',1);

INSERT INTO familiares(usuario_id,nome,cpf,telefone,email,parentesco_principal) VALUES (NULL,'Rafael Santos','80000012505','11900000125','contato125@example.com','Neto');

INSERT INTO idosos_familiares(idoso_id,familiar_id,parentesco,responsavel_principal) VALUES (@demo_idoso_25,LAST_INSERT_ID(),'Neto',0);

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_25,@demo_saude,'[Demonstração] Participou da atividade de música e convivência. Boa aceitação das refeições.',1,DATE_SUB(NOW(),INTERVAL 16 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_25,@demo_saude,'[Demonstração] Avaliação da equipe: manter acompanhamento e rotina de cuidados individualizada.',0,DATE_SUB(NOW(),INTERVAL 8 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_25,@demo_saude,'[Demonstração] Rotina tranquila, participou da caminhada assistida e recebeu contato da família.',1,DATE_SUB(NOW(),INTERVAL 2 DAY));

INSERT INTO idosos(nome,cpf,identificador,quarto_id,ativo,criado_em) VALUES ('Antônio Santos','70000002607','DEMO-026',@demo_quarto_7,1,DATE_SUB(NOW(),INTERVAL 154 DAY));

SET @demo_idoso_26 = LAST_INSERT_ID();

INSERT INTO familiares(usuario_id,nome,cpf,telefone,email,parentesco_principal) VALUES (NULL,'Beatriz Santos','80000002615','11900000026','contato26@example.com','Filha');

INSERT INTO idosos_familiares(idoso_id,familiar_id,parentesco,responsavel_principal) VALUES (@demo_idoso_26,LAST_INSERT_ID(),'Filha',1);

INSERT INTO familiares(usuario_id,nome,cpf,telefone,email,parentesco_principal) VALUES (NULL,'Beatriz Santos','80000012688','11900000126','contato126@example.com','Neto');

INSERT INTO idosos_familiares(idoso_id,familiar_id,parentesco,responsavel_principal) VALUES (@demo_idoso_26,LAST_INSERT_ID(),'Neto',0);

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_26,@demo_saude,'[Demonstração] Participou da atividade de música e convivência. Boa aceitação das refeições.',1,DATE_SUB(NOW(),INTERVAL 17 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_26,@demo_saude,'[Demonstração] Avaliação da equipe: manter acompanhamento e rotina de cuidados individualizada.',0,DATE_SUB(NOW(),INTERVAL 9 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_26,@demo_saude,'[Demonstração] Rotina tranquila, participou da caminhada assistida e recebeu contato da família.',1,DATE_SUB(NOW(),INTERVAL 3 DAY));

INSERT INTO idosos(nome,cpf,identificador,quarto_id,ativo,criado_em) VALUES ('Helena Santos','70000002798','DEMO-027',@demo_quarto_7,1,DATE_SUB(NOW(),INTERVAL 153 DAY));

SET @demo_idoso_27 = LAST_INSERT_ID();

INSERT INTO familiares(usuario_id,nome,cpf,telefone,email,parentesco_principal) VALUES (NULL,'Gabriel Santos','80000002704','11900000027','contato27@example.com','Filha');

INSERT INTO idosos_familiares(idoso_id,familiar_id,parentesco,responsavel_principal) VALUES (@demo_idoso_27,LAST_INSERT_ID(),'Filha',1);

INSERT INTO familiares(usuario_id,nome,cpf,telefone,email,parentesco_principal) VALUES (NULL,'Gabriel Santos','80000012769','11900000127','contato127@example.com','Neto');

INSERT INTO idosos_familiares(idoso_id,familiar_id,parentesco,responsavel_principal) VALUES (@demo_idoso_27,LAST_INSERT_ID(),'Neto',0);

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_27,@demo_saude,'[Demonstração] Participou da atividade de música e convivência. Boa aceitação das refeições.',1,DATE_SUB(NOW(),INTERVAL 15 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_27,@demo_saude,'[Demonstração] Avaliação da equipe: manter acompanhamento e rotina de cuidados individualizada.',0,DATE_SUB(NOW(),INTERVAL 7 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_27,@demo_saude,'[Demonstração] Rotina tranquila, participou da caminhada assistida e recebeu contato da família.',1,DATE_SUB(NOW(),INTERVAL 1 DAY));

INSERT INTO idosos(nome,cpf,identificador,quarto_id,ativo,criado_em) VALUES ('Manuel Santos','70000002879','DEMO-028',@demo_quarto_7,1,DATE_SUB(NOW(),INTERVAL 152 DAY));

SET @demo_idoso_28 = LAST_INSERT_ID();

INSERT INTO familiares(usuario_id,nome,cpf,telefone,email,parentesco_principal) VALUES (NULL,'Carolina Santos','80000002887','11900000028','contato28@example.com','Filha');

INSERT INTO idosos_familiares(idoso_id,familiar_id,parentesco,responsavel_principal) VALUES (@demo_idoso_28,LAST_INSERT_ID(),'Filha',1);

INSERT INTO familiares(usuario_id,nome,cpf,telefone,email,parentesco_principal) VALUES (NULL,'Carolina Santos','80000012840','11900000128','contato128@example.com','Neto');

INSERT INTO idosos_familiares(idoso_id,familiar_id,parentesco,responsavel_principal) VALUES (@demo_idoso_28,LAST_INSERT_ID(),'Neto',0);

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_28,@demo_saude,'[Demonstração] Participou da atividade de música e convivência. Boa aceitação das refeições.',1,DATE_SUB(NOW(),INTERVAL 16 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_28,@demo_saude,'[Demonstração] Avaliação da equipe: manter acompanhamento e rotina de cuidados individualizada.',0,DATE_SUB(NOW(),INTERVAL 8 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_28,@demo_saude,'[Demonstração] Rotina tranquila, participou da caminhada assistida e recebeu contato da família.',1,DATE_SUB(NOW(),INTERVAL 2 DAY));

INSERT INTO idosos(nome,cpf,identificador,quarto_id,ativo,criado_em) VALUES ('Lúcia Santos','70000002950','DEMO-029',@demo_quarto_8,1,DATE_SUB(NOW(),INTERVAL 151 DAY));

SET @demo_idoso_29 = LAST_INSERT_ID();

INSERT INTO familiares(usuario_id,nome,cpf,telefone,email,parentesco_principal) VALUES (NULL,'Rafael Santos','80000002968','11900000029','contato29@example.com','Filha');

INSERT INTO idosos_familiares(idoso_id,familiar_id,parentesco,responsavel_principal) VALUES (@demo_idoso_29,LAST_INSERT_ID(),'Filha',1);

INSERT INTO familiares(usuario_id,nome,cpf,telefone,email,parentesco_principal) VALUES (NULL,'Rafael Santos','80000012920','11900000129','contato129@example.com','Neto');

INSERT INTO idosos_familiares(idoso_id,familiar_id,parentesco,responsavel_principal) VALUES (@demo_idoso_29,LAST_INSERT_ID(),'Neto',0);

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_29,@demo_saude,'[Demonstração] Participou da atividade de música e convivência. Boa aceitação das refeições.',1,DATE_SUB(NOW(),INTERVAL 17 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_29,@demo_saude,'[Demonstração] Avaliação da equipe: manter acompanhamento e rotina de cuidados individualizada.',0,DATE_SUB(NOW(),INTERVAL 9 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_29,@demo_saude,'[Demonstração] Rotina tranquila, participou da caminhada assistida e recebeu contato da família.',1,DATE_SUB(NOW(),INTERVAL 3 DAY));

INSERT INTO idosos(nome,cpf,identificador,quarto_id,ativo,criado_em) VALUES ('Pedro Santos','70000003093','DEMO-030',@demo_quarto_8,1,DATE_SUB(NOW(),INTERVAL 150 DAY));

SET @demo_idoso_30 = LAST_INSERT_ID();

INSERT INTO familiares(usuario_id,nome,cpf,telefone,email,parentesco_principal) VALUES (NULL,'Beatriz Santos','80000003000','11900000030','contato30@example.com','Filha');

INSERT INTO idosos_familiares(idoso_id,familiar_id,parentesco,responsavel_principal) VALUES (@demo_idoso_30,LAST_INSERT_ID(),'Filha',1);

INSERT INTO familiares(usuario_id,nome,cpf,telefone,email,parentesco_principal) VALUES (NULL,'Beatriz Santos','80000013064','11900000130','contato130@example.com','Neto');

INSERT INTO idosos_familiares(idoso_id,familiar_id,parentesco,responsavel_principal) VALUES (@demo_idoso_30,LAST_INSERT_ID(),'Neto',0);

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_30,@demo_saude,'[Demonstração] Participou da atividade de música e convivência. Boa aceitação das refeições.',1,DATE_SUB(NOW(),INTERVAL 15 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_30,@demo_saude,'[Demonstração] Avaliação da equipe: manter acompanhamento e rotina de cuidados individualizada.',0,DATE_SUB(NOW(),INTERVAL 7 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_30,@demo_saude,'[Demonstração] Rotina tranquila, participou da caminhada assistida e recebeu contato da família.',1,DATE_SUB(NOW(),INTERVAL 1 DAY));

INSERT INTO idosos(nome,cpf,identificador,quarto_id,ativo,criado_em) VALUES ('Tereza Santos','70000003174','DEMO-031',@demo_quarto_8,1,DATE_SUB(NOW(),INTERVAL 149 DAY));

SET @demo_idoso_31 = LAST_INSERT_ID();

INSERT INTO familiares(usuario_id,nome,cpf,telefone,email,parentesco_principal) VALUES (NULL,'Gabriel Santos','80000003182','11900000031','contato31@example.com','Filha');

INSERT INTO idosos_familiares(idoso_id,familiar_id,parentesco,responsavel_principal) VALUES (@demo_idoso_31,LAST_INSERT_ID(),'Filha',1);

INSERT INTO familiares(usuario_id,nome,cpf,telefone,email,parentesco_principal) VALUES (NULL,'Gabriel Santos','80000013145','11900000131','contato131@example.com','Neto');

INSERT INTO idosos_familiares(idoso_id,familiar_id,parentesco,responsavel_principal) VALUES (@demo_idoso_31,LAST_INSERT_ID(),'Neto',0);

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_31,@demo_saude,'[Demonstração] Participou da atividade de música e convivência. Boa aceitação das refeições.',1,DATE_SUB(NOW(),INTERVAL 16 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_31,@demo_saude,'[Demonstração] Avaliação da equipe: manter acompanhamento e rotina de cuidados individualizada.',0,DATE_SUB(NOW(),INTERVAL 8 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_31,@demo_saude,'[Demonstração] Rotina tranquila, participou da caminhada assistida e recebeu contato da família.',1,DATE_SUB(NOW(),INTERVAL 2 DAY));

INSERT INTO idosos(nome,cpf,identificador,quarto_id,ativo,criado_em) VALUES ('Luiz Santos','70000003255','DEMO-032',@demo_quarto_8,1,DATE_SUB(NOW(),INTERVAL 148 DAY));

SET @demo_idoso_32 = LAST_INSERT_ID();

INSERT INTO familiares(usuario_id,nome,cpf,telefone,email,parentesco_principal) VALUES (NULL,'Carolina Santos','80000003263','11900000032','contato32@example.com','Filha');

INSERT INTO idosos_familiares(idoso_id,familiar_id,parentesco,responsavel_principal) VALUES (@demo_idoso_32,LAST_INSERT_ID(),'Filha',1);

INSERT INTO familiares(usuario_id,nome,cpf,telefone,email,parentesco_principal) VALUES (NULL,'Carolina Santos','80000013226','11900000132','contato132@example.com','Neto');

INSERT INTO idosos_familiares(idoso_id,familiar_id,parentesco,responsavel_principal) VALUES (@demo_idoso_32,LAST_INSERT_ID(),'Neto',0);

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_32,@demo_saude,'[Demonstração] Participou da atividade de música e convivência. Boa aceitação das refeições.',1,DATE_SUB(NOW(),INTERVAL 17 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_32,@demo_saude,'[Demonstração] Avaliação da equipe: manter acompanhamento e rotina de cuidados individualizada.',0,DATE_SUB(NOW(),INTERVAL 9 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_32,@demo_saude,'[Demonstração] Rotina tranquila, participou da caminhada assistida e recebeu contato da família.',1,DATE_SUB(NOW(),INTERVAL 3 DAY));

INSERT INTO idosos(nome,cpf,identificador,quarto_id,ativo,criado_em) VALUES ('Rosa Santos','70000003336','DEMO-033',@demo_quarto_9,1,DATE_SUB(NOW(),INTERVAL 147 DAY));

SET @demo_idoso_33 = LAST_INSERT_ID();

INSERT INTO familiares(usuario_id,nome,cpf,telefone,email,parentesco_principal) VALUES (NULL,'Rafael Santos','80000003344','11900000033','contato33@example.com','Filha');

INSERT INTO idosos_familiares(idoso_id,familiar_id,parentesco,responsavel_principal) VALUES (@demo_idoso_33,LAST_INSERT_ID(),'Filha',1);

INSERT INTO familiares(usuario_id,nome,cpf,telefone,email,parentesco_principal) VALUES (NULL,'Rafael Santos','80000013307','11900000133','contato133@example.com','Neto');

INSERT INTO idosos_familiares(idoso_id,familiar_id,parentesco,responsavel_principal) VALUES (@demo_idoso_33,LAST_INSERT_ID(),'Neto',0);

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_33,@demo_saude,'[Demonstração] Participou da atividade de música e convivência. Boa aceitação das refeições.',1,DATE_SUB(NOW(),INTERVAL 15 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_33,@demo_saude,'[Demonstração] Avaliação da equipe: manter acompanhamento e rotina de cuidados individualizada.',0,DATE_SUB(NOW(),INTERVAL 7 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_33,@demo_saude,'[Demonstração] Rotina tranquila, participou da caminhada assistida e recebeu contato da família.',1,DATE_SUB(NOW(),INTERVAL 1 DAY));

INSERT INTO idosos(nome,cpf,identificador,quarto_id,ativo,criado_em) VALUES ('Paulo Santos','70000003417','DEMO-034',@demo_quarto_9,1,DATE_SUB(NOW(),INTERVAL 146 DAY));

SET @demo_idoso_34 = LAST_INSERT_ID();

INSERT INTO familiares(usuario_id,nome,cpf,telefone,email,parentesco_principal) VALUES (NULL,'Beatriz Santos','80000003425','11900000034','contato34@example.com','Filha');

INSERT INTO idosos_familiares(idoso_id,familiar_id,parentesco,responsavel_principal) VALUES (@demo_idoso_34,LAST_INSERT_ID(),'Filha',1);

INSERT INTO familiares(usuario_id,nome,cpf,telefone,email,parentesco_principal) VALUES (NULL,'Beatriz Santos','80000013498','11900000134','contato134@example.com','Neto');

INSERT INTO idosos_familiares(idoso_id,familiar_id,parentesco,responsavel_principal) VALUES (@demo_idoso_34,LAST_INSERT_ID(),'Neto',0);

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_34,@demo_saude,'[Demonstração] Participou da atividade de música e convivência. Boa aceitação das refeições.',1,DATE_SUB(NOW(),INTERVAL 16 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_34,@demo_saude,'[Demonstração] Avaliação da equipe: manter acompanhamento e rotina de cuidados individualizada.',0,DATE_SUB(NOW(),INTERVAL 8 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_34,@demo_saude,'[Demonstração] Rotina tranquila, participou da caminhada assistida e recebeu contato da família.',1,DATE_SUB(NOW(),INTERVAL 2 DAY));

INSERT INTO idosos(nome,cpf,identificador,quarto_id,ativo,criado_em) VALUES ('Célia Santos','70000003506','DEMO-035',@demo_quarto_9,1,DATE_SUB(NOW(),INTERVAL 145 DAY));

SET @demo_idoso_35 = LAST_INSERT_ID();

INSERT INTO familiares(usuario_id,nome,cpf,telefone,email,parentesco_principal) VALUES (NULL,'Gabriel Santos','80000003506','11900000035','contato35@example.com','Filha');

INSERT INTO idosos_familiares(idoso_id,familiar_id,parentesco,responsavel_principal) VALUES (@demo_idoso_35,LAST_INSERT_ID(),'Filha',1);

INSERT INTO familiares(usuario_id,nome,cpf,telefone,email,parentesco_principal) VALUES (NULL,'Gabriel Santos','80000013579','11900000135','contato135@example.com','Neto');

INSERT INTO idosos_familiares(idoso_id,familiar_id,parentesco,responsavel_principal) VALUES (@demo_idoso_35,LAST_INSERT_ID(),'Neto',0);

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_35,@demo_saude,'[Demonstração] Participou da atividade de música e convivência. Boa aceitação das refeições.',1,DATE_SUB(NOW(),INTERVAL 17 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_35,@demo_saude,'[Demonstração] Avaliação da equipe: manter acompanhamento e rotina de cuidados individualizada.',0,DATE_SUB(NOW(),INTERVAL 9 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_35,@demo_saude,'[Demonstração] Rotina tranquila, participou da caminhada assistida e recebeu contato da família.',1,DATE_SUB(NOW(),INTERVAL 3 DAY));

INSERT INTO idosos(nome,cpf,identificador,quarto_id,ativo,criado_em) VALUES ('Joaquim Santos','70000003689','DEMO-036',@demo_quarto_9,1,DATE_SUB(NOW(),INTERVAL 144 DAY));

SET @demo_idoso_36 = LAST_INSERT_ID();

INSERT INTO familiares(usuario_id,nome,cpf,telefone,email,parentesco_principal) VALUES (NULL,'Carolina Santos','80000003697','11900000036','contato36@example.com','Filha');

INSERT INTO idosos_familiares(idoso_id,familiar_id,parentesco,responsavel_principal) VALUES (@demo_idoso_36,LAST_INSERT_ID(),'Filha',1);

INSERT INTO familiares(usuario_id,nome,cpf,telefone,email,parentesco_principal) VALUES (NULL,'Carolina Santos','80000013650','11900000136','contato136@example.com','Neto');

INSERT INTO idosos_familiares(idoso_id,familiar_id,parentesco,responsavel_principal) VALUES (@demo_idoso_36,LAST_INSERT_ID(),'Neto',0);

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_36,@demo_saude,'[Demonstração] Participou da atividade de música e convivência. Boa aceitação das refeições.',1,DATE_SUB(NOW(),INTERVAL 15 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_36,@demo_saude,'[Demonstração] Avaliação da equipe: manter acompanhamento e rotina de cuidados individualizada.',0,DATE_SUB(NOW(),INTERVAL 7 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_36,@demo_saude,'[Demonstração] Rotina tranquila, participou da caminhada assistida e recebeu contato da família.',1,DATE_SUB(NOW(),INTERVAL 1 DAY));

INSERT INTO idosos(nome,cpf,identificador,quarto_id,ativo,criado_em) VALUES ('Rita Santos','70000003760','DEMO-037',@demo_quarto_10,1,DATE_SUB(NOW(),INTERVAL 143 DAY));

SET @demo_idoso_37 = LAST_INSERT_ID();

INSERT INTO familiares(usuario_id,nome,cpf,telefone,email,parentesco_principal) VALUES (NULL,'Rafael Santos','80000003778','11900000037','contato37@example.com','Filha');

INSERT INTO idosos_familiares(idoso_id,familiar_id,parentesco,responsavel_principal) VALUES (@demo_idoso_37,LAST_INSERT_ID(),'Filha',1);

INSERT INTO familiares(usuario_id,nome,cpf,telefone,email,parentesco_principal) VALUES (NULL,'Rafael Santos','80000013730','11900000137','contato137@example.com','Neto');

INSERT INTO idosos_familiares(idoso_id,familiar_id,parentesco,responsavel_principal) VALUES (@demo_idoso_37,LAST_INSERT_ID(),'Neto',0);

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_37,@demo_saude,'[Demonstração] Participou da atividade de música e convivência. Boa aceitação das refeições.',1,DATE_SUB(NOW(),INTERVAL 16 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_37,@demo_saude,'[Demonstração] Avaliação da equipe: manter acompanhamento e rotina de cuidados individualizada.',0,DATE_SUB(NOW(),INTERVAL 8 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_37,@demo_saude,'[Demonstração] Rotina tranquila, participou da caminhada assistida e recebeu contato da família.',1,DATE_SUB(NOW(),INTERVAL 2 DAY));

INSERT INTO idosos(nome,cpf,identificador,quarto_id,ativo,criado_em) VALUES ('Sebastião Santos','70000003840','DEMO-038',@demo_quarto_10,1,DATE_SUB(NOW(),INTERVAL 142 DAY));

SET @demo_idoso_38 = LAST_INSERT_ID();

INSERT INTO familiares(usuario_id,nome,cpf,telefone,email,parentesco_principal) VALUES (NULL,'Beatriz Santos','80000003859','11900000038','contato38@example.com','Filha');

INSERT INTO idosos_familiares(idoso_id,familiar_id,parentesco,responsavel_principal) VALUES (@demo_idoso_38,LAST_INSERT_ID(),'Filha',1);

INSERT INTO familiares(usuario_id,nome,cpf,telefone,email,parentesco_principal) VALUES (NULL,'Beatriz Santos','80000013811','11900000138','contato138@example.com','Neto');

INSERT INTO idosos_familiares(idoso_id,familiar_id,parentesco,responsavel_principal) VALUES (@demo_idoso_38,LAST_INSERT_ID(),'Neto',0);

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_38,@demo_saude,'[Demonstração] Participou da atividade de música e convivência. Boa aceitação das refeições.',1,DATE_SUB(NOW(),INTERVAL 17 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_38,@demo_saude,'[Demonstração] Avaliação da equipe: manter acompanhamento e rotina de cuidados individualizada.',0,DATE_SUB(NOW(),INTERVAL 9 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_38,@demo_saude,'[Demonstração] Rotina tranquila, participou da caminhada assistida e recebeu contato da família.',1,DATE_SUB(NOW(),INTERVAL 3 DAY));

INSERT INTO idosos(nome,cpf,identificador,quarto_id,ativo,criado_em) VALUES ('Alice Santos','70000003921','DEMO-039',@demo_quarto_10,1,DATE_SUB(NOW(),INTERVAL 141 DAY));

SET @demo_idoso_39 = LAST_INSERT_ID();

INSERT INTO familiares(usuario_id,nome,cpf,telefone,email,parentesco_principal) VALUES (NULL,'Gabriel Santos','80000003930','11900000039','contato39@example.com','Filha');

INSERT INTO idosos_familiares(idoso_id,familiar_id,parentesco,responsavel_principal) VALUES (@demo_idoso_39,LAST_INSERT_ID(),'Filha',1);

INSERT INTO familiares(usuario_id,nome,cpf,telefone,email,parentesco_principal) VALUES (NULL,'Gabriel Santos','80000013900','11900000139','contato139@example.com','Neto');

INSERT INTO idosos_familiares(idoso_id,familiar_id,parentesco,responsavel_principal) VALUES (@demo_idoso_39,LAST_INSERT_ID(),'Neto',0);

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_39,@demo_saude,'[Demonstração] Participou da atividade de música e convivência. Boa aceitação das refeições.',1,DATE_SUB(NOW(),INTERVAL 15 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_39,@demo_saude,'[Demonstração] Avaliação da equipe: manter acompanhamento e rotina de cuidados individualizada.',0,DATE_SUB(NOW(),INTERVAL 7 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_39,@demo_saude,'[Demonstração] Rotina tranquila, participou da caminhada assistida e recebeu contato da família.',1,DATE_SUB(NOW(),INTERVAL 1 DAY));

INSERT INTO idosos(nome,cpf,identificador,quarto_id,ativo,criado_em) VALUES ('Francisco Santos','70000004065','DEMO-040',@demo_quarto_10,1,DATE_SUB(NOW(),INTERVAL 140 DAY));

SET @demo_idoso_40 = LAST_INSERT_ID();

INSERT INTO familiares(usuario_id,nome,cpf,telefone,email,parentesco_principal) VALUES (NULL,'Carolina Santos','80000004073','11900000040','contato40@example.com','Filha');

INSERT INTO idosos_familiares(idoso_id,familiar_id,parentesco,responsavel_principal) VALUES (@demo_idoso_40,LAST_INSERT_ID(),'Filha',1);

INSERT INTO familiares(usuario_id,nome,cpf,telefone,email,parentesco_principal) VALUES (NULL,'Carolina Santos','80000014036','11900000140','contato140@example.com','Neto');

INSERT INTO idosos_familiares(idoso_id,familiar_id,parentesco,responsavel_principal) VALUES (@demo_idoso_40,LAST_INSERT_ID(),'Neto',0);

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_40,@demo_saude,'[Demonstração] Participou da atividade de música e convivência. Boa aceitação das refeições.',1,DATE_SUB(NOW(),INTERVAL 16 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_40,@demo_saude,'[Demonstração] Avaliação da equipe: manter acompanhamento e rotina de cuidados individualizada.',0,DATE_SUB(NOW(),INTERVAL 8 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_40,@demo_saude,'[Demonstração] Rotina tranquila, participou da caminhada assistida e recebeu contato da família.',1,DATE_SUB(NOW(),INTERVAL 2 DAY));

INSERT INTO idosos(nome,cpf,identificador,quarto_id,ativo,criado_em) VALUES ('Maria Pereira','70000004146','DEMO-041',@demo_quarto_11,1,DATE_SUB(NOW(),INTERVAL 139 DAY));

SET @demo_idoso_41 = LAST_INSERT_ID();

INSERT INTO familiares(usuario_id,nome,cpf,telefone,email,parentesco_principal) VALUES (NULL,'Rafael Pereira','80000004154','11900000041','contato41@example.com','Filha');

INSERT INTO idosos_familiares(idoso_id,familiar_id,parentesco,responsavel_principal) VALUES (@demo_idoso_41,LAST_INSERT_ID(),'Filha',1);

INSERT INTO familiares(usuario_id,nome,cpf,telefone,email,parentesco_principal) VALUES (NULL,'Rafael Pereira','80000014117','11900000141','contato141@example.com','Neto');

INSERT INTO idosos_familiares(idoso_id,familiar_id,parentesco,responsavel_principal) VALUES (@demo_idoso_41,LAST_INSERT_ID(),'Neto',0);

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_41,@demo_saude,'[Demonstração] Participou da atividade de música e convivência. Boa aceitação das refeições.',1,DATE_SUB(NOW(),INTERVAL 17 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_41,@demo_saude,'[Demonstração] Avaliação da equipe: manter acompanhamento e rotina de cuidados individualizada.',0,DATE_SUB(NOW(),INTERVAL 9 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_41,@demo_saude,'[Demonstração] Rotina tranquila, participou da caminhada assistida e recebeu contato da família.',1,DATE_SUB(NOW(),INTERVAL 3 DAY));

INSERT INTO idosos(nome,cpf,identificador,quarto_id,ativo,criado_em) VALUES ('José Pereira','70000004227','DEMO-042',@demo_quarto_11,1,DATE_SUB(NOW(),INTERVAL 138 DAY));

SET @demo_idoso_42 = LAST_INSERT_ID();

INSERT INTO familiares(usuario_id,nome,cpf,telefone,email,parentesco_principal) VALUES (NULL,'Beatriz Pereira','80000004235','11900000042','contato42@example.com','Filha');

INSERT INTO idosos_familiares(idoso_id,familiar_id,parentesco,responsavel_principal) VALUES (@demo_idoso_42,LAST_INSERT_ID(),'Filha',1);

INSERT INTO familiares(usuario_id,nome,cpf,telefone,email,parentesco_principal) VALUES (NULL,'Beatriz Pereira','80000014206','11900000142','contato142@example.com','Neto');

INSERT INTO idosos_familiares(idoso_id,familiar_id,parentesco,responsavel_principal) VALUES (@demo_idoso_42,LAST_INSERT_ID(),'Neto',0);

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_42,@demo_saude,'[Demonstração] Participou da atividade de música e convivência. Boa aceitação das refeições.',1,DATE_SUB(NOW(),INTERVAL 15 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_42,@demo_saude,'[Demonstração] Avaliação da equipe: manter acompanhamento e rotina de cuidados individualizada.',0,DATE_SUB(NOW(),INTERVAL 7 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_42,@demo_saude,'[Demonstração] Rotina tranquila, participou da caminhada assistida e recebeu contato da família.',1,DATE_SUB(NOW(),INTERVAL 1 DAY));

INSERT INTO idosos(nome,cpf,identificador,quarto_id,ativo,criado_em) VALUES ('Ana Pereira','70000004308','DEMO-043',@demo_quarto_11,1,DATE_SUB(NOW(),INTERVAL 137 DAY));

SET @demo_idoso_43 = LAST_INSERT_ID();

INSERT INTO familiares(usuario_id,nome,cpf,telefone,email,parentesco_principal) VALUES (NULL,'Gabriel Pereira','80000004316','11900000043','contato43@example.com','Filha');

INSERT INTO idosos_familiares(idoso_id,familiar_id,parentesco,responsavel_principal) VALUES (@demo_idoso_43,LAST_INSERT_ID(),'Filha',1);

INSERT INTO familiares(usuario_id,nome,cpf,telefone,email,parentesco_principal) VALUES (NULL,'Gabriel Pereira','80000014389','11900000143','contato143@example.com','Neto');

INSERT INTO idosos_familiares(idoso_id,familiar_id,parentesco,responsavel_principal) VALUES (@demo_idoso_43,LAST_INSERT_ID(),'Neto',0);

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_43,@demo_saude,'[Demonstração] Participou da atividade de música e convivência. Boa aceitação das refeições.',1,DATE_SUB(NOW(),INTERVAL 16 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_43,@demo_saude,'[Demonstração] Avaliação da equipe: manter acompanhamento e rotina de cuidados individualizada.',0,DATE_SUB(NOW(),INTERVAL 8 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_43,@demo_saude,'[Demonstração] Rotina tranquila, participou da caminhada assistida e recebeu contato da família.',1,DATE_SUB(NOW(),INTERVAL 2 DAY));

INSERT INTO idosos(nome,cpf,identificador,quarto_id,ativo,criado_em) VALUES ('João Pereira','70000004499','DEMO-044',@demo_quarto_11,1,DATE_SUB(NOW(),INTERVAL 136 DAY));

SET @demo_idoso_44 = LAST_INSERT_ID();

INSERT INTO familiares(usuario_id,nome,cpf,telefone,email,parentesco_principal) VALUES (NULL,'Carolina Pereira','80000004405','11900000044','contato44@example.com','Filha');

INSERT INTO idosos_familiares(idoso_id,familiar_id,parentesco,responsavel_principal) VALUES (@demo_idoso_44,LAST_INSERT_ID(),'Filha',1);

INSERT INTO familiares(usuario_id,nome,cpf,telefone,email,parentesco_principal) VALUES (NULL,'Carolina Pereira','80000014460','11900000144','contato144@example.com','Neto');

INSERT INTO idosos_familiares(idoso_id,familiar_id,parentesco,responsavel_principal) VALUES (@demo_idoso_44,LAST_INSERT_ID(),'Neto',0);

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_44,@demo_saude,'[Demonstração] Participou da atividade de música e convivência. Boa aceitação das refeições.',1,DATE_SUB(NOW(),INTERVAL 17 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_44,@demo_saude,'[Demonstração] Avaliação da equipe: manter acompanhamento e rotina de cuidados individualizada.',0,DATE_SUB(NOW(),INTERVAL 9 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_44,@demo_saude,'[Demonstração] Rotina tranquila, participou da caminhada assistida e recebeu contato da família.',1,DATE_SUB(NOW(),INTERVAL 3 DAY));

INSERT INTO idosos(nome,cpf,identificador,quarto_id,ativo,criado_em) VALUES ('Francisca Pereira','70000004570','DEMO-045',@demo_quarto_12,1,DATE_SUB(NOW(),INTERVAL 135 DAY));

SET @demo_idoso_45 = LAST_INSERT_ID();

INSERT INTO familiares(usuario_id,nome,cpf,telefone,email,parentesco_principal) VALUES (NULL,'Rafael Pereira','80000004588','11900000045','contato45@example.com','Filha');

INSERT INTO idosos_familiares(idoso_id,familiar_id,parentesco,responsavel_principal) VALUES (@demo_idoso_45,LAST_INSERT_ID(),'Filha',1);

INSERT INTO familiares(usuario_id,nome,cpf,telefone,email,parentesco_principal) VALUES (NULL,'Rafael Pereira','80000014540','11900000145','contato145@example.com','Neto');

INSERT INTO idosos_familiares(idoso_id,familiar_id,parentesco,responsavel_principal) VALUES (@demo_idoso_45,LAST_INSERT_ID(),'Neto',0);

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_45,@demo_saude,'[Demonstração] Participou da atividade de música e convivência. Boa aceitação das refeições.',1,DATE_SUB(NOW(),INTERVAL 15 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_45,@demo_saude,'[Demonstração] Avaliação da equipe: manter acompanhamento e rotina de cuidados individualizada.',0,DATE_SUB(NOW(),INTERVAL 7 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_45,@demo_saude,'[Demonstração] Rotina tranquila, participou da caminhada assistida e recebeu contato da família.',1,DATE_SUB(NOW(),INTERVAL 1 DAY));

INSERT INTO idosos(nome,cpf,identificador,quarto_id,ativo,criado_em) VALUES ('Antônio Pereira','70000004650','DEMO-046',@demo_quarto_12,1,DATE_SUB(NOW(),INTERVAL 134 DAY));

SET @demo_idoso_46 = LAST_INSERT_ID();

INSERT INTO familiares(usuario_id,nome,cpf,telefone,email,parentesco_principal) VALUES (NULL,'Beatriz Pereira','80000004669','11900000046','contato46@example.com','Filha');

INSERT INTO idosos_familiares(idoso_id,familiar_id,parentesco,responsavel_principal) VALUES (@demo_idoso_46,LAST_INSERT_ID(),'Filha',1);

INSERT INTO familiares(usuario_id,nome,cpf,telefone,email,parentesco_principal) VALUES (NULL,'Beatriz Pereira','80000014621','11900000146','contato146@example.com','Neto');

INSERT INTO idosos_familiares(idoso_id,familiar_id,parentesco,responsavel_principal) VALUES (@demo_idoso_46,LAST_INSERT_ID(),'Neto',0);

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_46,@demo_saude,'[Demonstração] Participou da atividade de música e convivência. Boa aceitação das refeições.',1,DATE_SUB(NOW(),INTERVAL 16 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_46,@demo_saude,'[Demonstração] Avaliação da equipe: manter acompanhamento e rotina de cuidados individualizada.',0,DATE_SUB(NOW(),INTERVAL 8 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_46,@demo_saude,'[Demonstração] Rotina tranquila, participou da caminhada assistida e recebeu contato da família.',1,DATE_SUB(NOW(),INTERVAL 2 DAY));

INSERT INTO idosos(nome,cpf,identificador,quarto_id,ativo,criado_em) VALUES ('Helena Pereira','70000004731','DEMO-047',@demo_quarto_12,1,DATE_SUB(NOW(),INTERVAL 133 DAY));

SET @demo_idoso_47 = LAST_INSERT_ID();

INSERT INTO familiares(usuario_id,nome,cpf,telefone,email,parentesco_principal) VALUES (NULL,'Gabriel Pereira','80000004740','11900000047','contato47@example.com','Filha');

INSERT INTO idosos_familiares(idoso_id,familiar_id,parentesco,responsavel_principal) VALUES (@demo_idoso_47,LAST_INSERT_ID(),'Filha',1);

INSERT INTO familiares(usuario_id,nome,cpf,telefone,email,parentesco_principal) VALUES (NULL,'Gabriel Pereira','80000014702','11900000147','contato147@example.com','Neto');

INSERT INTO idosos_familiares(idoso_id,familiar_id,parentesco,responsavel_principal) VALUES (@demo_idoso_47,LAST_INSERT_ID(),'Neto',0);

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_47,@demo_saude,'[Demonstração] Participou da atividade de música e convivência. Boa aceitação das refeições.',1,DATE_SUB(NOW(),INTERVAL 17 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_47,@demo_saude,'[Demonstração] Avaliação da equipe: manter acompanhamento e rotina de cuidados individualizada.',0,DATE_SUB(NOW(),INTERVAL 9 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_47,@demo_saude,'[Demonstração] Rotina tranquila, participou da caminhada assistida e recebeu contato da família.',1,DATE_SUB(NOW(),INTERVAL 3 DAY));

INSERT INTO idosos(nome,cpf,identificador,quarto_id,ativo,criado_em) VALUES ('Manuel Pereira','70000004812','DEMO-048',@demo_quarto_12,1,DATE_SUB(NOW(),INTERVAL 132 DAY));

SET @demo_idoso_48 = LAST_INSERT_ID();

INSERT INTO familiares(usuario_id,nome,cpf,telefone,email,parentesco_principal) VALUES (NULL,'Carolina Pereira','80000004820','11900000048','contato48@example.com','Filha');

INSERT INTO idosos_familiares(idoso_id,familiar_id,parentesco,responsavel_principal) VALUES (@demo_idoso_48,LAST_INSERT_ID(),'Filha',1);

INSERT INTO familiares(usuario_id,nome,cpf,telefone,email,parentesco_principal) VALUES (NULL,'Carolina Pereira','80000014893','11900000148','contato148@example.com','Neto');

INSERT INTO idosos_familiares(idoso_id,familiar_id,parentesco,responsavel_principal) VALUES (@demo_idoso_48,LAST_INSERT_ID(),'Neto',0);

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_48,@demo_saude,'[Demonstração] Participou da atividade de música e convivência. Boa aceitação das refeições.',1,DATE_SUB(NOW(),INTERVAL 15 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_48,@demo_saude,'[Demonstração] Avaliação da equipe: manter acompanhamento e rotina de cuidados individualizada.',0,DATE_SUB(NOW(),INTERVAL 7 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_48,@demo_saude,'[Demonstração] Rotina tranquila, participou da caminhada assistida e recebeu contato da família.',1,DATE_SUB(NOW(),INTERVAL 1 DAY));

INSERT INTO idosos(nome,cpf,identificador,quarto_id,ativo,criado_em) VALUES ('Lúcia Pereira','70000004901','DEMO-049',@demo_quarto_13,1,DATE_SUB(NOW(),INTERVAL 131 DAY));

SET @demo_idoso_49 = LAST_INSERT_ID();

INSERT INTO familiares(usuario_id,nome,cpf,telefone,email,parentesco_principal) VALUES (NULL,'Rafael Pereira','80000004901','11900000049','contato49@example.com','Filha');

INSERT INTO idosos_familiares(idoso_id,familiar_id,parentesco,responsavel_principal) VALUES (@demo_idoso_49,LAST_INSERT_ID(),'Filha',1);

INSERT INTO familiares(usuario_id,nome,cpf,telefone,email,parentesco_principal) VALUES (NULL,'Rafael Pereira','80000014974','11900000149','contato149@example.com','Neto');

INSERT INTO idosos_familiares(idoso_id,familiar_id,parentesco,responsavel_principal) VALUES (@demo_idoso_49,LAST_INSERT_ID(),'Neto',0);

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_49,@demo_saude,'[Demonstração] Participou da atividade de música e convivência. Boa aceitação das refeições.',1,DATE_SUB(NOW(),INTERVAL 16 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_49,@demo_saude,'[Demonstração] Avaliação da equipe: manter acompanhamento e rotina de cuidados individualizada.',0,DATE_SUB(NOW(),INTERVAL 8 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_49,@demo_saude,'[Demonstração] Rotina tranquila, participou da caminhada assistida e recebeu contato da família.',1,DATE_SUB(NOW(),INTERVAL 2 DAY));

INSERT INTO idosos(nome,cpf,identificador,quarto_id,ativo,criado_em) VALUES ('Pedro Pereira','70000005037','DEMO-050',@demo_quarto_13,1,DATE_SUB(NOW(),INTERVAL 130 DAY));

SET @demo_idoso_50 = LAST_INSERT_ID();

INSERT INTO familiares(usuario_id,nome,cpf,telefone,email,parentesco_principal) VALUES (NULL,'Beatriz Pereira','80000005045','11900000050','contato50@example.com','Filha');

INSERT INTO idosos_familiares(idoso_id,familiar_id,parentesco,responsavel_principal) VALUES (@demo_idoso_50,LAST_INSERT_ID(),'Filha',1);

INSERT INTO familiares(usuario_id,nome,cpf,telefone,email,parentesco_principal) VALUES (NULL,'Beatriz Pereira','80000015008','11900000150','contato150@example.com','Neto');

INSERT INTO idosos_familiares(idoso_id,familiar_id,parentesco,responsavel_principal) VALUES (@demo_idoso_50,LAST_INSERT_ID(),'Neto',0);

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_50,@demo_saude,'[Demonstração] Participou da atividade de música e convivência. Boa aceitação das refeições.',1,DATE_SUB(NOW(),INTERVAL 17 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_50,@demo_saude,'[Demonstração] Avaliação da equipe: manter acompanhamento e rotina de cuidados individualizada.',0,DATE_SUB(NOW(),INTERVAL 9 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_50,@demo_saude,'[Demonstração] Rotina tranquila, participou da caminhada assistida e recebeu contato da família.',1,DATE_SUB(NOW(),INTERVAL 3 DAY));

INSERT INTO idosos(nome,cpf,identificador,quarto_id,ativo,criado_em) VALUES ('Tereza Pereira','70000005118','DEMO-051',@demo_quarto_13,1,DATE_SUB(NOW(),INTERVAL 129 DAY));

SET @demo_idoso_51 = LAST_INSERT_ID();

INSERT INTO familiares(usuario_id,nome,cpf,telefone,email,parentesco_principal) VALUES (NULL,'Gabriel Pereira','80000005126','11900000051','contato51@example.com','Filha');

INSERT INTO idosos_familiares(idoso_id,familiar_id,parentesco,responsavel_principal) VALUES (@demo_idoso_51,LAST_INSERT_ID(),'Filha',1);

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_51,@demo_saude,'[Demonstração] Participou da atividade de música e convivência. Boa aceitação das refeições.',1,DATE_SUB(NOW(),INTERVAL 15 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_51,@demo_saude,'[Demonstração] Avaliação da equipe: manter acompanhamento e rotina de cuidados individualizada.',0,DATE_SUB(NOW(),INTERVAL 7 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_51,@demo_saude,'[Demonstração] Rotina tranquila, participou da caminhada assistida e recebeu contato da família.',1,DATE_SUB(NOW(),INTERVAL 1 DAY));

INSERT INTO idosos(nome,cpf,identificador,quarto_id,ativo,criado_em) VALUES ('Luiz Pereira','70000005207','DEMO-052',@demo_quarto_13,1,DATE_SUB(NOW(),INTERVAL 128 DAY));

SET @demo_idoso_52 = LAST_INSERT_ID();

INSERT INTO familiares(usuario_id,nome,cpf,telefone,email,parentesco_principal) VALUES (NULL,'Carolina Pereira','80000005207','11900000052','contato52@example.com','Filha');

INSERT INTO idosos_familiares(idoso_id,familiar_id,parentesco,responsavel_principal) VALUES (@demo_idoso_52,LAST_INSERT_ID(),'Filha',1);

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_52,@demo_saude,'[Demonstração] Participou da atividade de música e convivência. Boa aceitação das refeições.',1,DATE_SUB(NOW(),INTERVAL 16 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_52,@demo_saude,'[Demonstração] Avaliação da equipe: manter acompanhamento e rotina de cuidados individualizada.',0,DATE_SUB(NOW(),INTERVAL 8 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_52,@demo_saude,'[Demonstração] Rotina tranquila, participou da caminhada assistida e recebeu contato da família.',1,DATE_SUB(NOW(),INTERVAL 2 DAY));

INSERT INTO idosos(nome,cpf,identificador,quarto_id,ativo,criado_em) VALUES ('Rosa Pereira','70000005380','DEMO-053',@demo_quarto_14,1,DATE_SUB(NOW(),INTERVAL 127 DAY));

SET @demo_idoso_53 = LAST_INSERT_ID();

INSERT INTO familiares(usuario_id,nome,cpf,telefone,email,parentesco_principal) VALUES (NULL,'Rafael Pereira','80000005398','11900000053','contato53@example.com','Filha');

INSERT INTO idosos_familiares(idoso_id,familiar_id,parentesco,responsavel_principal) VALUES (@demo_idoso_53,LAST_INSERT_ID(),'Filha',1);

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_53,@demo_saude,'[Demonstração] Participou da atividade de música e convivência. Boa aceitação das refeições.',1,DATE_SUB(NOW(),INTERVAL 17 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_53,@demo_saude,'[Demonstração] Avaliação da equipe: manter acompanhamento e rotina de cuidados individualizada.',0,DATE_SUB(NOW(),INTERVAL 9 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_53,@demo_saude,'[Demonstração] Rotina tranquila, participou da caminhada assistida e recebeu contato da família.',1,DATE_SUB(NOW(),INTERVAL 3 DAY));

INSERT INTO idosos(nome,cpf,identificador,quarto_id,ativo,criado_em) VALUES ('Paulo Pereira','70000005460','DEMO-054',@demo_quarto_14,1,DATE_SUB(NOW(),INTERVAL 126 DAY));

SET @demo_idoso_54 = LAST_INSERT_ID();

INSERT INTO familiares(usuario_id,nome,cpf,telefone,email,parentesco_principal) VALUES (NULL,'Beatriz Pereira','80000005479','11900000054','contato54@example.com','Filha');

INSERT INTO idosos_familiares(idoso_id,familiar_id,parentesco,responsavel_principal) VALUES (@demo_idoso_54,LAST_INSERT_ID(),'Filha',1);

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_54,@demo_saude,'[Demonstração] Participou da atividade de música e convivência. Boa aceitação das refeições.',1,DATE_SUB(NOW(),INTERVAL 15 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_54,@demo_saude,'[Demonstração] Avaliação da equipe: manter acompanhamento e rotina de cuidados individualizada.',0,DATE_SUB(NOW(),INTERVAL 7 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_54,@demo_saude,'[Demonstração] Rotina tranquila, participou da caminhada assistida e recebeu contato da família.',1,DATE_SUB(NOW(),INTERVAL 1 DAY));

INSERT INTO idosos(nome,cpf,identificador,quarto_id,ativo,criado_em) VALUES ('Célia Pereira','70000005541','DEMO-055',@demo_quarto_14,1,DATE_SUB(NOW(),INTERVAL 125 DAY));

SET @demo_idoso_55 = LAST_INSERT_ID();

INSERT INTO familiares(usuario_id,nome,cpf,telefone,email,parentesco_principal) VALUES (NULL,'Gabriel Pereira','80000005550','11900000055','contato55@example.com','Filha');

INSERT INTO idosos_familiares(idoso_id,familiar_id,parentesco,responsavel_principal) VALUES (@demo_idoso_55,LAST_INSERT_ID(),'Filha',1);

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_55,@demo_saude,'[Demonstração] Participou da atividade de música e convivência. Boa aceitação das refeições.',1,DATE_SUB(NOW(),INTERVAL 16 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_55,@demo_saude,'[Demonstração] Avaliação da equipe: manter acompanhamento e rotina de cuidados individualizada.',0,DATE_SUB(NOW(),INTERVAL 8 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_55,@demo_saude,'[Demonstração] Rotina tranquila, participou da caminhada assistida e recebeu contato da família.',1,DATE_SUB(NOW(),INTERVAL 2 DAY));

INSERT INTO idosos(nome,cpf,identificador,quarto_id,ativo,criado_em) VALUES ('Joaquim Pereira','70000005622','DEMO-056',@demo_quarto_14,1,DATE_SUB(NOW(),INTERVAL 124 DAY));

SET @demo_idoso_56 = LAST_INSERT_ID();

INSERT INTO familiares(usuario_id,nome,cpf,telefone,email,parentesco_principal) VALUES (NULL,'Carolina Pereira','80000005630','11900000056','contato56@example.com','Filha');

INSERT INTO idosos_familiares(idoso_id,familiar_id,parentesco,responsavel_principal) VALUES (@demo_idoso_56,LAST_INSERT_ID(),'Filha',1);

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_56,@demo_saude,'[Demonstração] Participou da atividade de música e convivência. Boa aceitação das refeições.',1,DATE_SUB(NOW(),INTERVAL 17 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_56,@demo_saude,'[Demonstração] Avaliação da equipe: manter acompanhamento e rotina de cuidados individualizada.',0,DATE_SUB(NOW(),INTERVAL 9 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_56,@demo_saude,'[Demonstração] Rotina tranquila, participou da caminhada assistida e recebeu contato da família.',1,DATE_SUB(NOW(),INTERVAL 3 DAY));

INSERT INTO idosos(nome,cpf,identificador,quarto_id,ativo,criado_em) VALUES ('Rita Pereira','70000005703','DEMO-057',@demo_quarto_15,1,DATE_SUB(NOW(),INTERVAL 123 DAY));

SET @demo_idoso_57 = LAST_INSERT_ID();

INSERT INTO familiares(usuario_id,nome,cpf,telefone,email,parentesco_principal) VALUES (NULL,'Rafael Pereira','80000005711','11900000057','contato57@example.com','Filha');

INSERT INTO idosos_familiares(idoso_id,familiar_id,parentesco,responsavel_principal) VALUES (@demo_idoso_57,LAST_INSERT_ID(),'Filha',1);

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_57,@demo_saude,'[Demonstração] Participou da atividade de música e convivência. Boa aceitação das refeições.',1,DATE_SUB(NOW(),INTERVAL 15 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_57,@demo_saude,'[Demonstração] Avaliação da equipe: manter acompanhamento e rotina de cuidados individualizada.',0,DATE_SUB(NOW(),INTERVAL 7 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_57,@demo_saude,'[Demonstração] Rotina tranquila, participou da caminhada assistida e recebeu contato da família.',1,DATE_SUB(NOW(),INTERVAL 1 DAY));

INSERT INTO idosos(nome,cpf,identificador,quarto_id,ativo,criado_em) VALUES ('Sebastião Pereira','70000005894','DEMO-058',@demo_quarto_15,1,DATE_SUB(NOW(),INTERVAL 122 DAY));

SET @demo_idoso_58 = LAST_INSERT_ID();

INSERT INTO familiares(usuario_id,nome,cpf,telefone,email,parentesco_principal) VALUES (NULL,'Beatriz Pereira','80000005800','11900000058','contato58@example.com','Filha');

INSERT INTO idosos_familiares(idoso_id,familiar_id,parentesco,responsavel_principal) VALUES (@demo_idoso_58,LAST_INSERT_ID(),'Filha',1);

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_58,@demo_saude,'[Demonstração] Participou da atividade de música e convivência. Boa aceitação das refeições.',1,DATE_SUB(NOW(),INTERVAL 16 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_58,@demo_saude,'[Demonstração] Avaliação da equipe: manter acompanhamento e rotina de cuidados individualizada.',0,DATE_SUB(NOW(),INTERVAL 8 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_58,@demo_saude,'[Demonstração] Rotina tranquila, participou da caminhada assistida e recebeu contato da família.',1,DATE_SUB(NOW(),INTERVAL 2 DAY));

INSERT INTO idosos(nome,cpf,identificador,quarto_id,ativo,criado_em) VALUES ('Alice Pereira','70000005975','DEMO-059',@demo_quarto_15,1,DATE_SUB(NOW(),INTERVAL 121 DAY));

SET @demo_idoso_59 = LAST_INSERT_ID();

INSERT INTO familiares(usuario_id,nome,cpf,telefone,email,parentesco_principal) VALUES (NULL,'Gabriel Pereira','80000005983','11900000059','contato59@example.com','Filha');

INSERT INTO idosos_familiares(idoso_id,familiar_id,parentesco,responsavel_principal) VALUES (@demo_idoso_59,LAST_INSERT_ID(),'Filha',1);

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_59,@demo_saude,'[Demonstração] Participou da atividade de música e convivência. Boa aceitação das refeições.',1,DATE_SUB(NOW(),INTERVAL 17 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_59,@demo_saude,'[Demonstração] Avaliação da equipe: manter acompanhamento e rotina de cuidados individualizada.',0,DATE_SUB(NOW(),INTERVAL 9 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_59,@demo_saude,'[Demonstração] Rotina tranquila, participou da caminhada assistida e recebeu contato da família.',1,DATE_SUB(NOW(),INTERVAL 3 DAY));

INSERT INTO idosos(nome,cpf,identificador,quarto_id,ativo,criado_em) VALUES ('Francisco Pereira','70000006009','DEMO-060',@demo_quarto_15,1,DATE_SUB(NOW(),INTERVAL 120 DAY));

SET @demo_idoso_60 = LAST_INSERT_ID();

INSERT INTO familiares(usuario_id,nome,cpf,telefone,email,parentesco_principal) VALUES (NULL,'Carolina Pereira','80000006017','11900000060','contato60@example.com','Filha');

INSERT INTO idosos_familiares(idoso_id,familiar_id,parentesco,responsavel_principal) VALUES (@demo_idoso_60,LAST_INSERT_ID(),'Filha',1);

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_60,@demo_saude,'[Demonstração] Participou da atividade de música e convivência. Boa aceitação das refeições.',1,DATE_SUB(NOW(),INTERVAL 15 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_60,@demo_saude,'[Demonstração] Avaliação da equipe: manter acompanhamento e rotina de cuidados individualizada.',0,DATE_SUB(NOW(),INTERVAL 7 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_60,@demo_saude,'[Demonstração] Rotina tranquila, participou da caminhada assistida e recebeu contato da família.',1,DATE_SUB(NOW(),INTERVAL 1 DAY));

INSERT INTO idosos(nome,cpf,identificador,quarto_id,ativo,criado_em) VALUES ('Maria Costa','70000006190','DEMO-061',@demo_quarto_16,1,DATE_SUB(NOW(),INTERVAL 119 DAY));

SET @demo_idoso_61 = LAST_INSERT_ID();

INSERT INTO familiares(usuario_id,nome,cpf,telefone,email,parentesco_principal) VALUES (NULL,'Rafael Costa','80000006106','11900000061','contato61@example.com','Filha');

INSERT INTO idosos_familiares(idoso_id,familiar_id,parentesco,responsavel_principal) VALUES (@demo_idoso_61,LAST_INSERT_ID(),'Filha',1);

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_61,@demo_saude,'[Demonstração] Participou da atividade de música e convivência. Boa aceitação das refeições.',1,DATE_SUB(NOW(),INTERVAL 16 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_61,@demo_saude,'[Demonstração] Avaliação da equipe: manter acompanhamento e rotina de cuidados individualizada.',0,DATE_SUB(NOW(),INTERVAL 8 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_61,@demo_saude,'[Demonstração] Rotina tranquila, participou da caminhada assistida e recebeu contato da família.',1,DATE_SUB(NOW(),INTERVAL 2 DAY));

INSERT INTO idosos(nome,cpf,identificador,quarto_id,ativo,criado_em) VALUES ('José Costa','70000006270','DEMO-062',@demo_quarto_16,1,DATE_SUB(NOW(),INTERVAL 118 DAY));

SET @demo_idoso_62 = LAST_INSERT_ID();

INSERT INTO familiares(usuario_id,nome,cpf,telefone,email,parentesco_principal) VALUES (NULL,'Beatriz Costa','80000006289','11900000062','contato62@example.com','Filha');

INSERT INTO idosos_familiares(idoso_id,familiar_id,parentesco,responsavel_principal) VALUES (@demo_idoso_62,LAST_INSERT_ID(),'Filha',1);

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_62,@demo_saude,'[Demonstração] Participou da atividade de música e convivência. Boa aceitação das refeições.',1,DATE_SUB(NOW(),INTERVAL 17 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_62,@demo_saude,'[Demonstração] Avaliação da equipe: manter acompanhamento e rotina de cuidados individualizada.',0,DATE_SUB(NOW(),INTERVAL 9 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_62,@demo_saude,'[Demonstração] Rotina tranquila, participou da caminhada assistida e recebeu contato da família.',1,DATE_SUB(NOW(),INTERVAL 3 DAY));

INSERT INTO idosos(nome,cpf,identificador,quarto_id,ativo,criado_em) VALUES ('Ana Costa','70000006351','DEMO-063',@demo_quarto_16,1,DATE_SUB(NOW(),INTERVAL 117 DAY));

SET @demo_idoso_63 = LAST_INSERT_ID();

INSERT INTO familiares(usuario_id,nome,cpf,telefone,email,parentesco_principal) VALUES (NULL,'Gabriel Costa','80000006360','11900000063','contato63@example.com','Filha');

INSERT INTO idosos_familiares(idoso_id,familiar_id,parentesco,responsavel_principal) VALUES (@demo_idoso_63,LAST_INSERT_ID(),'Filha',1);

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_63,@demo_saude,'[Demonstração] Participou da atividade de música e convivência. Boa aceitação das refeições.',1,DATE_SUB(NOW(),INTERVAL 15 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_63,@demo_saude,'[Demonstração] Avaliação da equipe: manter acompanhamento e rotina de cuidados individualizada.',0,DATE_SUB(NOW(),INTERVAL 7 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_63,@demo_saude,'[Demonstração] Rotina tranquila, participou da caminhada assistida e recebeu contato da família.',1,DATE_SUB(NOW(),INTERVAL 1 DAY));

INSERT INTO idosos(nome,cpf,identificador,quarto_id,ativo,criado_em) VALUES ('João Costa','70000006432','DEMO-064',@demo_quarto_16,1,DATE_SUB(NOW(),INTERVAL 116 DAY));

SET @demo_idoso_64 = LAST_INSERT_ID();

INSERT INTO familiares(usuario_id,nome,cpf,telefone,email,parentesco_principal) VALUES (NULL,'Carolina Costa','80000006440','11900000064','contato64@example.com','Filha');

INSERT INTO idosos_familiares(idoso_id,familiar_id,parentesco,responsavel_principal) VALUES (@demo_idoso_64,LAST_INSERT_ID(),'Filha',1);

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_64,@demo_saude,'[Demonstração] Participou da atividade de música e convivência. Boa aceitação das refeições.',1,DATE_SUB(NOW(),INTERVAL 16 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_64,@demo_saude,'[Demonstração] Avaliação da equipe: manter acompanhamento e rotina de cuidados individualizada.',0,DATE_SUB(NOW(),INTERVAL 8 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_64,@demo_saude,'[Demonstração] Rotina tranquila, participou da caminhada assistida e recebeu contato da família.',1,DATE_SUB(NOW(),INTERVAL 2 DAY));

INSERT INTO idosos(nome,cpf,identificador,quarto_id,ativo,criado_em) VALUES ('Francisca Costa','70000006513','DEMO-065',@demo_quarto_17,1,DATE_SUB(NOW(),INTERVAL 115 DAY));

SET @demo_idoso_65 = LAST_INSERT_ID();

INSERT INTO familiares(usuario_id,nome,cpf,telefone,email,parentesco_principal) VALUES (NULL,'Rafael Costa','80000006521','11900000065','contato65@example.com','Filha');

INSERT INTO idosos_familiares(idoso_id,familiar_id,parentesco,responsavel_principal) VALUES (@demo_idoso_65,LAST_INSERT_ID(),'Filha',1);

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_65,@demo_saude,'[Demonstração] Participou da atividade de música e convivência. Boa aceitação das refeições.',1,DATE_SUB(NOW(),INTERVAL 17 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_65,@demo_saude,'[Demonstração] Avaliação da equipe: manter acompanhamento e rotina de cuidados individualizada.',0,DATE_SUB(NOW(),INTERVAL 9 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_65,@demo_saude,'[Demonstração] Rotina tranquila, participou da caminhada assistida e recebeu contato da família.',1,DATE_SUB(NOW(),INTERVAL 3 DAY));

INSERT INTO idosos(nome,cpf,identificador,quarto_id,ativo,criado_em) VALUES ('Antônio Costa','70000006602','DEMO-066',@demo_quarto_17,1,DATE_SUB(NOW(),INTERVAL 114 DAY));

SET @demo_idoso_66 = LAST_INSERT_ID();

INSERT INTO familiares(usuario_id,nome,cpf,telefone,email,parentesco_principal) VALUES (NULL,'Beatriz Costa','80000006602','11900000066','contato66@example.com','Filha');

INSERT INTO idosos_familiares(idoso_id,familiar_id,parentesco,responsavel_principal) VALUES (@demo_idoso_66,LAST_INSERT_ID(),'Filha',1);

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_66,@demo_saude,'[Demonstração] Participou da atividade de música e convivência. Boa aceitação das refeições.',1,DATE_SUB(NOW(),INTERVAL 15 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_66,@demo_saude,'[Demonstração] Avaliação da equipe: manter acompanhamento e rotina de cuidados individualizada.',0,DATE_SUB(NOW(),INTERVAL 7 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_66,@demo_saude,'[Demonstração] Rotina tranquila, participou da caminhada assistida e recebeu contato da família.',1,DATE_SUB(NOW(),INTERVAL 1 DAY));

INSERT INTO idosos(nome,cpf,identificador,quarto_id,ativo,criado_em) VALUES ('Helena Costa','70000006785','DEMO-067',@demo_quarto_17,1,DATE_SUB(NOW(),INTERVAL 113 DAY));

SET @demo_idoso_67 = LAST_INSERT_ID();

INSERT INTO familiares(usuario_id,nome,cpf,telefone,email,parentesco_principal) VALUES (NULL,'Gabriel Costa','80000006793','11900000067','contato67@example.com','Filha');

INSERT INTO idosos_familiares(idoso_id,familiar_id,parentesco,responsavel_principal) VALUES (@demo_idoso_67,LAST_INSERT_ID(),'Filha',1);

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_67,@demo_saude,'[Demonstração] Participou da atividade de música e convivência. Boa aceitação das refeições.',1,DATE_SUB(NOW(),INTERVAL 16 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_67,@demo_saude,'[Demonstração] Avaliação da equipe: manter acompanhamento e rotina de cuidados individualizada.',0,DATE_SUB(NOW(),INTERVAL 8 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_67,@demo_saude,'[Demonstração] Rotina tranquila, participou da caminhada assistida e recebeu contato da família.',1,DATE_SUB(NOW(),INTERVAL 2 DAY));

INSERT INTO idosos(nome,cpf,identificador,quarto_id,ativo,criado_em) VALUES ('Manuel Costa','70000006866','DEMO-068',@demo_quarto_17,1,DATE_SUB(NOW(),INTERVAL 112 DAY));

SET @demo_idoso_68 = LAST_INSERT_ID();

INSERT INTO familiares(usuario_id,nome,cpf,telefone,email,parentesco_principal) VALUES (NULL,'Carolina Costa','80000006874','11900000068','contato68@example.com','Filha');

INSERT INTO idosos_familiares(idoso_id,familiar_id,parentesco,responsavel_principal) VALUES (@demo_idoso_68,LAST_INSERT_ID(),'Filha',1);

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_68,@demo_saude,'[Demonstração] Participou da atividade de música e convivência. Boa aceitação das refeições.',1,DATE_SUB(NOW(),INTERVAL 17 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_68,@demo_saude,'[Demonstração] Avaliação da equipe: manter acompanhamento e rotina de cuidados individualizada.',0,DATE_SUB(NOW(),INTERVAL 9 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_68,@demo_saude,'[Demonstração] Rotina tranquila, participou da caminhada assistida e recebeu contato da família.',1,DATE_SUB(NOW(),INTERVAL 3 DAY));

INSERT INTO idosos(nome,cpf,identificador,quarto_id,ativo,criado_em) VALUES ('Lúcia Costa','70000006947','DEMO-069',@demo_quarto_18,1,DATE_SUB(NOW(),INTERVAL 111 DAY));

SET @demo_idoso_69 = LAST_INSERT_ID();

INSERT INTO familiares(usuario_id,nome,cpf,telefone,email,parentesco_principal) VALUES (NULL,'Rafael Costa','80000006955','11900000069','contato69@example.com','Filha');

INSERT INTO idosos_familiares(idoso_id,familiar_id,parentesco,responsavel_principal) VALUES (@demo_idoso_69,LAST_INSERT_ID(),'Filha',1);

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_69,@demo_saude,'[Demonstração] Participou da atividade de música e convivência. Boa aceitação das refeições.',1,DATE_SUB(NOW(),INTERVAL 15 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_69,@demo_saude,'[Demonstração] Avaliação da equipe: manter acompanhamento e rotina de cuidados individualizada.',0,DATE_SUB(NOW(),INTERVAL 7 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_69,@demo_saude,'[Demonstração] Rotina tranquila, participou da caminhada assistida e recebeu contato da família.',1,DATE_SUB(NOW(),INTERVAL 1 DAY));

INSERT INTO idosos(nome,cpf,identificador,quarto_id,ativo,criado_em) VALUES ('Pedro Costa','70000007080','DEMO-070',@demo_quarto_18,1,DATE_SUB(NOW(),INTERVAL 110 DAY));

SET @demo_idoso_70 = LAST_INSERT_ID();

INSERT INTO familiares(usuario_id,nome,cpf,telefone,email,parentesco_principal) VALUES (NULL,'Beatriz Costa','80000007099','11900000070','contato70@example.com','Filha');

INSERT INTO idosos_familiares(idoso_id,familiar_id,parentesco,responsavel_principal) VALUES (@demo_idoso_70,LAST_INSERT_ID(),'Filha',1);

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_70,@demo_saude,'[Demonstração] Participou da atividade de música e convivência. Boa aceitação das refeições.',1,DATE_SUB(NOW(),INTERVAL 16 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_70,@demo_saude,'[Demonstração] Avaliação da equipe: manter acompanhamento e rotina de cuidados individualizada.',0,DATE_SUB(NOW(),INTERVAL 8 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_70,@demo_saude,'[Demonstração] Rotina tranquila, participou da caminhada assistida e recebeu contato da família.',1,DATE_SUB(NOW(),INTERVAL 2 DAY));

INSERT INTO idosos(nome,cpf,identificador,quarto_id,ativo,criado_em) VALUES ('Tereza Costa','70000007161','DEMO-071',@demo_quarto_18,1,DATE_SUB(NOW(),INTERVAL 109 DAY));

SET @demo_idoso_71 = LAST_INSERT_ID();

INSERT INTO familiares(usuario_id,nome,cpf,telefone,email,parentesco_principal) VALUES (NULL,'Gabriel Costa','80000007170','11900000071','contato71@example.com','Filha');

INSERT INTO idosos_familiares(idoso_id,familiar_id,parentesco,responsavel_principal) VALUES (@demo_idoso_71,LAST_INSERT_ID(),'Filha',1);

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_71,@demo_saude,'[Demonstração] Participou da atividade de música e convivência. Boa aceitação das refeições.',1,DATE_SUB(NOW(),INTERVAL 17 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_71,@demo_saude,'[Demonstração] Avaliação da equipe: manter acompanhamento e rotina de cuidados individualizada.',0,DATE_SUB(NOW(),INTERVAL 9 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_71,@demo_saude,'[Demonstração] Rotina tranquila, participou da caminhada assistida e recebeu contato da família.',1,DATE_SUB(NOW(),INTERVAL 3 DAY));

INSERT INTO idosos(nome,cpf,identificador,quarto_id,ativo,criado_em) VALUES ('Luiz Costa','70000007242','DEMO-072',@demo_quarto_18,1,DATE_SUB(NOW(),INTERVAL 108 DAY));

SET @demo_idoso_72 = LAST_INSERT_ID();

INSERT INTO familiares(usuario_id,nome,cpf,telefone,email,parentesco_principal) VALUES (NULL,'Carolina Costa','80000007250','11900000072','contato72@example.com','Filha');

INSERT INTO idosos_familiares(idoso_id,familiar_id,parentesco,responsavel_principal) VALUES (@demo_idoso_72,LAST_INSERT_ID(),'Filha',1);

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_72,@demo_saude,'[Demonstração] Participou da atividade de música e convivência. Boa aceitação das refeições.',1,DATE_SUB(NOW(),INTERVAL 15 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_72,@demo_saude,'[Demonstração] Avaliação da equipe: manter acompanhamento e rotina de cuidados individualizada.',0,DATE_SUB(NOW(),INTERVAL 7 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_72,@demo_saude,'[Demonstração] Rotina tranquila, participou da caminhada assistida e recebeu contato da família.',1,DATE_SUB(NOW(),INTERVAL 1 DAY));

INSERT INTO idosos(nome,cpf,identificador,quarto_id,ativo,criado_em) VALUES ('Rosa Costa','70000007323','DEMO-073',@demo_quarto_19,1,DATE_SUB(NOW(),INTERVAL 107 DAY));

SET @demo_idoso_73 = LAST_INSERT_ID();

INSERT INTO familiares(usuario_id,nome,cpf,telefone,email,parentesco_principal) VALUES (NULL,'Rafael Costa','80000007331','11900000073','contato73@example.com','Filha');

INSERT INTO idosos_familiares(idoso_id,familiar_id,parentesco,responsavel_principal) VALUES (@demo_idoso_73,LAST_INSERT_ID(),'Filha',1);

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_73,@demo_saude,'[Demonstração] Participou da atividade de música e convivência. Boa aceitação das refeições.',1,DATE_SUB(NOW(),INTERVAL 16 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_73,@demo_saude,'[Demonstração] Avaliação da equipe: manter acompanhamento e rotina de cuidados individualizada.',0,DATE_SUB(NOW(),INTERVAL 8 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_73,@demo_saude,'[Demonstração] Rotina tranquila, participou da caminhada assistida e recebeu contato da família.',1,DATE_SUB(NOW(),INTERVAL 2 DAY));

INSERT INTO idosos(nome,cpf,identificador,quarto_id,ativo,criado_em) VALUES ('Paulo Costa','70000007404','DEMO-074',@demo_quarto_19,1,DATE_SUB(NOW(),INTERVAL 106 DAY));

SET @demo_idoso_74 = LAST_INSERT_ID();

INSERT INTO familiares(usuario_id,nome,cpf,telefone,email,parentesco_principal) VALUES (NULL,'Beatriz Costa','80000007412','11900000074','contato74@example.com','Filha');

INSERT INTO idosos_familiares(idoso_id,familiar_id,parentesco,responsavel_principal) VALUES (@demo_idoso_74,LAST_INSERT_ID(),'Filha',1);

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_74,@demo_saude,'[Demonstração] Participou da atividade de música e convivência. Boa aceitação das refeições.',1,DATE_SUB(NOW(),INTERVAL 17 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_74,@demo_saude,'[Demonstração] Avaliação da equipe: manter acompanhamento e rotina de cuidados individualizada.',0,DATE_SUB(NOW(),INTERVAL 9 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_74,@demo_saude,'[Demonstração] Rotina tranquila, participou da caminhada assistida e recebeu contato da família.',1,DATE_SUB(NOW(),INTERVAL 3 DAY));

INSERT INTO idosos(nome,cpf,identificador,quarto_id,ativo,criado_em) VALUES ('Célia Costa','70000007595','DEMO-075',@demo_quarto_19,1,DATE_SUB(NOW(),INTERVAL 105 DAY));

SET @demo_idoso_75 = LAST_INSERT_ID();

INSERT INTO familiares(usuario_id,nome,cpf,telefone,email,parentesco_principal) VALUES (NULL,'Gabriel Costa','80000007501','11900000075','contato75@example.com','Filha');

INSERT INTO idosos_familiares(idoso_id,familiar_id,parentesco,responsavel_principal) VALUES (@demo_idoso_75,LAST_INSERT_ID(),'Filha',1);

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_75,@demo_saude,'[Demonstração] Participou da atividade de música e convivência. Boa aceitação das refeições.',1,DATE_SUB(NOW(),INTERVAL 15 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_75,@demo_saude,'[Demonstração] Avaliação da equipe: manter acompanhamento e rotina de cuidados individualizada.',0,DATE_SUB(NOW(),INTERVAL 7 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_75,@demo_saude,'[Demonstração] Rotina tranquila, participou da caminhada assistida e recebeu contato da família.',1,DATE_SUB(NOW(),INTERVAL 1 DAY));

INSERT INTO idosos(nome,cpf,identificador,quarto_id,ativo,criado_em) VALUES ('Joaquim Costa','70000007676','DEMO-076',@demo_quarto_19,1,DATE_SUB(NOW(),INTERVAL 104 DAY));

SET @demo_idoso_76 = LAST_INSERT_ID();

INSERT INTO familiares(usuario_id,nome,cpf,telefone,email,parentesco_principal) VALUES (NULL,'Carolina Costa','80000007684','11900000076','contato76@example.com','Filha');

INSERT INTO idosos_familiares(idoso_id,familiar_id,parentesco,responsavel_principal) VALUES (@demo_idoso_76,LAST_INSERT_ID(),'Filha',1);

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_76,@demo_saude,'[Demonstração] Participou da atividade de música e convivência. Boa aceitação das refeições.',1,DATE_SUB(NOW(),INTERVAL 16 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_76,@demo_saude,'[Demonstração] Avaliação da equipe: manter acompanhamento e rotina de cuidados individualizada.',0,DATE_SUB(NOW(),INTERVAL 8 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_76,@demo_saude,'[Demonstração] Rotina tranquila, participou da caminhada assistida e recebeu contato da família.',1,DATE_SUB(NOW(),INTERVAL 2 DAY));

INSERT INTO idosos(nome,cpf,identificador,quarto_id,ativo,criado_em) VALUES ('Rita Costa','70000007757','DEMO-077',@demo_quarto_20,1,DATE_SUB(NOW(),INTERVAL 103 DAY));

SET @demo_idoso_77 = LAST_INSERT_ID();

INSERT INTO familiares(usuario_id,nome,cpf,telefone,email,parentesco_principal) VALUES (NULL,'Rafael Costa','80000007765','11900000077','contato77@example.com','Filha');

INSERT INTO idosos_familiares(idoso_id,familiar_id,parentesco,responsavel_principal) VALUES (@demo_idoso_77,LAST_INSERT_ID(),'Filha',1);

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_77,@demo_saude,'[Demonstração] Participou da atividade de música e convivência. Boa aceitação das refeições.',1,DATE_SUB(NOW(),INTERVAL 17 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_77,@demo_saude,'[Demonstração] Avaliação da equipe: manter acompanhamento e rotina de cuidados individualizada.',0,DATE_SUB(NOW(),INTERVAL 9 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_77,@demo_saude,'[Demonstração] Rotina tranquila, participou da caminhada assistida e recebeu contato da família.',1,DATE_SUB(NOW(),INTERVAL 3 DAY));

INSERT INTO idosos(nome,cpf,identificador,quarto_id,ativo,criado_em) VALUES ('Sebastião Costa','70000007838','DEMO-078',@demo_quarto_20,1,DATE_SUB(NOW(),INTERVAL 102 DAY));

SET @demo_idoso_78 = LAST_INSERT_ID();

INSERT INTO familiares(usuario_id,nome,cpf,telefone,email,parentesco_principal) VALUES (NULL,'Beatriz Costa','80000007846','11900000078','contato78@example.com','Filha');

INSERT INTO idosos_familiares(idoso_id,familiar_id,parentesco,responsavel_principal) VALUES (@demo_idoso_78,LAST_INSERT_ID(),'Filha',1);

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_78,@demo_saude,'[Demonstração] Participou da atividade de música e convivência. Boa aceitação das refeições.',1,DATE_SUB(NOW(),INTERVAL 15 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_78,@demo_saude,'[Demonstração] Avaliação da equipe: manter acompanhamento e rotina de cuidados individualizada.',0,DATE_SUB(NOW(),INTERVAL 7 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_78,@demo_saude,'[Demonstração] Rotina tranquila, participou da caminhada assistida e recebeu contato da família.',1,DATE_SUB(NOW(),INTERVAL 1 DAY));

INSERT INTO idosos(nome,cpf,identificador,quarto_id,ativo,criado_em) VALUES ('Alice Costa','70000007919','DEMO-079',@demo_quarto_20,1,DATE_SUB(NOW(),INTERVAL 101 DAY));

SET @demo_idoso_79 = LAST_INSERT_ID();

INSERT INTO familiares(usuario_id,nome,cpf,telefone,email,parentesco_principal) VALUES (NULL,'Gabriel Costa','80000007927','11900000079','contato79@example.com','Filha');

INSERT INTO idosos_familiares(idoso_id,familiar_id,parentesco,responsavel_principal) VALUES (@demo_idoso_79,LAST_INSERT_ID(),'Filha',1);

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_79,@demo_saude,'[Demonstração] Participou da atividade de música e convivência. Boa aceitação das refeições.',1,DATE_SUB(NOW(),INTERVAL 16 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_79,@demo_saude,'[Demonstração] Avaliação da equipe: manter acompanhamento e rotina de cuidados individualizada.',0,DATE_SUB(NOW(),INTERVAL 8 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_79,@demo_saude,'[Demonstração] Rotina tranquila, participou da caminhada assistida e recebeu contato da família.',1,DATE_SUB(NOW(),INTERVAL 2 DAY));

INSERT INTO idosos(nome,cpf,identificador,quarto_id,ativo,criado_em) VALUES ('Francisco Costa','70000008052','DEMO-080',@demo_quarto_20,1,DATE_SUB(NOW(),INTERVAL 100 DAY));

SET @demo_idoso_80 = LAST_INSERT_ID();

INSERT INTO familiares(usuario_id,nome,cpf,telefone,email,parentesco_principal) VALUES (NULL,'Carolina Costa','80000008060','11900000080','contato80@example.com','Filha');

INSERT INTO idosos_familiares(idoso_id,familiar_id,parentesco,responsavel_principal) VALUES (@demo_idoso_80,LAST_INSERT_ID(),'Filha',1);

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_80,@demo_saude,'[Demonstração] Participou da atividade de música e convivência. Boa aceitação das refeições.',1,DATE_SUB(NOW(),INTERVAL 17 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_80,@demo_saude,'[Demonstração] Avaliação da equipe: manter acompanhamento e rotina de cuidados individualizada.',0,DATE_SUB(NOW(),INTERVAL 9 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_80,@demo_saude,'[Demonstração] Rotina tranquila, participou da caminhada assistida e recebeu contato da família.',1,DATE_SUB(NOW(),INTERVAL 3 DAY));

INSERT INTO idosos(nome,cpf,identificador,quarto_id,ativo,criado_em) VALUES ('Maria Almeida','70000008133','DEMO-081',@demo_quarto_21,1,DATE_SUB(NOW(),INTERVAL 99 DAY));

SET @demo_idoso_81 = LAST_INSERT_ID();

INSERT INTO familiares(usuario_id,nome,cpf,telefone,email,parentesco_principal) VALUES (NULL,'Rafael Almeida','80000008141','11900000081','contato81@example.com','Filha');

INSERT INTO idosos_familiares(idoso_id,familiar_id,parentesco,responsavel_principal) VALUES (@demo_idoso_81,LAST_INSERT_ID(),'Filha',1);

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_81,@demo_saude,'[Demonstração] Participou da atividade de música e convivência. Boa aceitação das refeições.',1,DATE_SUB(NOW(),INTERVAL 15 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_81,@demo_saude,'[Demonstração] Avaliação da equipe: manter acompanhamento e rotina de cuidados individualizada.',0,DATE_SUB(NOW(),INTERVAL 7 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_81,@demo_saude,'[Demonstração] Rotina tranquila, participou da caminhada assistida e recebeu contato da família.',1,DATE_SUB(NOW(),INTERVAL 1 DAY));

INSERT INTO idosos(nome,cpf,identificador,quarto_id,ativo,criado_em) VALUES ('José Almeida','70000008214','DEMO-082',@demo_quarto_21,1,DATE_SUB(NOW(),INTERVAL 98 DAY));

SET @demo_idoso_82 = LAST_INSERT_ID();

INSERT INTO familiares(usuario_id,nome,cpf,telefone,email,parentesco_principal) VALUES (NULL,'Beatriz Almeida','80000008222','11900000082','contato82@example.com','Filha');

INSERT INTO idosos_familiares(idoso_id,familiar_id,parentesco,responsavel_principal) VALUES (@demo_idoso_82,LAST_INSERT_ID(),'Filha',1);

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_82,@demo_saude,'[Demonstração] Participou da atividade de música e convivência. Boa aceitação das refeições.',1,DATE_SUB(NOW(),INTERVAL 16 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_82,@demo_saude,'[Demonstração] Avaliação da equipe: manter acompanhamento e rotina de cuidados individualizada.',0,DATE_SUB(NOW(),INTERVAL 8 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_82,@demo_saude,'[Demonstração] Rotina tranquila, participou da caminhada assistida e recebeu contato da família.',1,DATE_SUB(NOW(),INTERVAL 2 DAY));

INSERT INTO idosos(nome,cpf,identificador,quarto_id,ativo,criado_em) VALUES ('Ana Almeida','70000008303','DEMO-083',@demo_quarto_21,1,DATE_SUB(NOW(),INTERVAL 97 DAY));

SET @demo_idoso_83 = LAST_INSERT_ID();

INSERT INTO familiares(usuario_id,nome,cpf,telefone,email,parentesco_principal) VALUES (NULL,'Gabriel Almeida','80000008303','11900000083','contato83@example.com','Filha');

INSERT INTO idosos_familiares(idoso_id,familiar_id,parentesco,responsavel_principal) VALUES (@demo_idoso_83,LAST_INSERT_ID(),'Filha',1);

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_83,@demo_saude,'[Demonstração] Participou da atividade de música e convivência. Boa aceitação das refeições.',1,DATE_SUB(NOW(),INTERVAL 17 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_83,@demo_saude,'[Demonstração] Avaliação da equipe: manter acompanhamento e rotina de cuidados individualizada.',0,DATE_SUB(NOW(),INTERVAL 9 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_83,@demo_saude,'[Demonstração] Rotina tranquila, participou da caminhada assistida e recebeu contato da família.',1,DATE_SUB(NOW(),INTERVAL 3 DAY));

INSERT INTO idosos(nome,cpf,identificador,quarto_id,ativo,criado_em) VALUES ('João Almeida','70000008486','DEMO-084',@demo_quarto_21,1,DATE_SUB(NOW(),INTERVAL 96 DAY));

SET @demo_idoso_84 = LAST_INSERT_ID();

INSERT INTO familiares(usuario_id,nome,cpf,telefone,email,parentesco_principal) VALUES (NULL,'Carolina Almeida','80000008494','11900000084','contato84@example.com','Filha');

INSERT INTO idosos_familiares(idoso_id,familiar_id,parentesco,responsavel_principal) VALUES (@demo_idoso_84,LAST_INSERT_ID(),'Filha',1);

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_84,@demo_saude,'[Demonstração] Participou da atividade de música e convivência. Boa aceitação das refeições.',1,DATE_SUB(NOW(),INTERVAL 15 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_84,@demo_saude,'[Demonstração] Avaliação da equipe: manter acompanhamento e rotina de cuidados individualizada.',0,DATE_SUB(NOW(),INTERVAL 7 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_84,@demo_saude,'[Demonstração] Rotina tranquila, participou da caminhada assistida e recebeu contato da família.',1,DATE_SUB(NOW(),INTERVAL 1 DAY));

INSERT INTO idosos(nome,cpf,identificador,quarto_id,ativo,criado_em) VALUES ('Francisca Almeida','70000008567','DEMO-085',@demo_quarto_22,1,DATE_SUB(NOW(),INTERVAL 95 DAY));

SET @demo_idoso_85 = LAST_INSERT_ID();

INSERT INTO familiares(usuario_id,nome,cpf,telefone,email,parentesco_principal) VALUES (NULL,'Rafael Almeida','80000008575','11900000085','contato85@example.com','Filha');

INSERT INTO idosos_familiares(idoso_id,familiar_id,parentesco,responsavel_principal) VALUES (@demo_idoso_85,LAST_INSERT_ID(),'Filha',1);

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_85,@demo_saude,'[Demonstração] Participou da atividade de música e convivência. Boa aceitação das refeições.',1,DATE_SUB(NOW(),INTERVAL 16 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_85,@demo_saude,'[Demonstração] Avaliação da equipe: manter acompanhamento e rotina de cuidados individualizada.',0,DATE_SUB(NOW(),INTERVAL 8 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_85,@demo_saude,'[Demonstração] Rotina tranquila, participou da caminhada assistida e recebeu contato da família.',1,DATE_SUB(NOW(),INTERVAL 2 DAY));

INSERT INTO idosos(nome,cpf,identificador,quarto_id,ativo,criado_em) VALUES ('Antônio Almeida','70000008648','DEMO-086',@demo_quarto_22,1,DATE_SUB(NOW(),INTERVAL 94 DAY));

SET @demo_idoso_86 = LAST_INSERT_ID();

INSERT INTO familiares(usuario_id,nome,cpf,telefone,email,parentesco_principal) VALUES (NULL,'Beatriz Almeida','80000008656','11900000086','contato86@example.com','Filha');

INSERT INTO idosos_familiares(idoso_id,familiar_id,parentesco,responsavel_principal) VALUES (@demo_idoso_86,LAST_INSERT_ID(),'Filha',1);

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_86,@demo_saude,'[Demonstração] Participou da atividade de música e convivência. Boa aceitação das refeições.',1,DATE_SUB(NOW(),INTERVAL 17 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_86,@demo_saude,'[Demonstração] Avaliação da equipe: manter acompanhamento e rotina de cuidados individualizada.',0,DATE_SUB(NOW(),INTERVAL 9 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_86,@demo_saude,'[Demonstração] Rotina tranquila, participou da caminhada assistida e recebeu contato da família.',1,DATE_SUB(NOW(),INTERVAL 3 DAY));

INSERT INTO idosos(nome,cpf,identificador,quarto_id,ativo,criado_em) VALUES ('Helena Almeida','70000008729','DEMO-087',@demo_quarto_22,1,DATE_SUB(NOW(),INTERVAL 93 DAY));

SET @demo_idoso_87 = LAST_INSERT_ID();

INSERT INTO familiares(usuario_id,nome,cpf,telefone,email,parentesco_principal) VALUES (NULL,'Gabriel Almeida','80000008737','11900000087','contato87@example.com','Filha');

INSERT INTO idosos_familiares(idoso_id,familiar_id,parentesco,responsavel_principal) VALUES (@demo_idoso_87,LAST_INSERT_ID(),'Filha',1);

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_87,@demo_saude,'[Demonstração] Participou da atividade de música e convivência. Boa aceitação das refeições.',1,DATE_SUB(NOW(),INTERVAL 15 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_87,@demo_saude,'[Demonstração] Avaliação da equipe: manter acompanhamento e rotina de cuidados individualizada.',0,DATE_SUB(NOW(),INTERVAL 7 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_87,@demo_saude,'[Demonstração] Rotina tranquila, participou da caminhada assistida e recebeu contato da família.',1,DATE_SUB(NOW(),INTERVAL 1 DAY));

INSERT INTO idosos(nome,cpf,identificador,quarto_id,ativo,criado_em) VALUES ('Manuel Almeida','70000008800','DEMO-088',@demo_quarto_22,1,DATE_SUB(NOW(),INTERVAL 92 DAY));

SET @demo_idoso_88 = LAST_INSERT_ID();

INSERT INTO familiares(usuario_id,nome,cpf,telefone,email,parentesco_principal) VALUES (NULL,'Carolina Almeida','80000008818','11900000088','contato88@example.com','Filha');

INSERT INTO idosos_familiares(idoso_id,familiar_id,parentesco,responsavel_principal) VALUES (@demo_idoso_88,LAST_INSERT_ID(),'Filha',1);

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_88,@demo_saude,'[Demonstração] Participou da atividade de música e convivência. Boa aceitação das refeições.',1,DATE_SUB(NOW(),INTERVAL 16 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_88,@demo_saude,'[Demonstração] Avaliação da equipe: manter acompanhamento e rotina de cuidados individualizada.',0,DATE_SUB(NOW(),INTERVAL 8 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_88,@demo_saude,'[Demonstração] Rotina tranquila, participou da caminhada assistida e recebeu contato da família.',1,DATE_SUB(NOW(),INTERVAL 2 DAY));

INSERT INTO idosos(nome,cpf,identificador,quarto_id,ativo,criado_em) VALUES ('Lúcia Almeida','70000008990','DEMO-089',@demo_quarto_23,1,DATE_SUB(NOW(),INTERVAL 91 DAY));

SET @demo_idoso_89 = LAST_INSERT_ID();

INSERT INTO familiares(usuario_id,nome,cpf,telefone,email,parentesco_principal) VALUES (NULL,'Rafael Almeida','80000008907','11900000089','contato89@example.com','Filha');

INSERT INTO idosos_familiares(idoso_id,familiar_id,parentesco,responsavel_principal) VALUES (@demo_idoso_89,LAST_INSERT_ID(),'Filha',1);

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_89,@demo_saude,'[Demonstração] Participou da atividade de música e convivência. Boa aceitação das refeições.',1,DATE_SUB(NOW(),INTERVAL 17 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_89,@demo_saude,'[Demonstração] Avaliação da equipe: manter acompanhamento e rotina de cuidados individualizada.',0,DATE_SUB(NOW(),INTERVAL 9 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_89,@demo_saude,'[Demonstração] Rotina tranquila, participou da caminhada assistida e recebeu contato da família.',1,DATE_SUB(NOW(),INTERVAL 3 DAY));

INSERT INTO idosos(nome,cpf,identificador,quarto_id,ativo,criado_em) VALUES ('Pedro Almeida','70000009024','DEMO-090',@demo_quarto_23,1,DATE_SUB(NOW(),INTERVAL 90 DAY));

SET @demo_idoso_90 = LAST_INSERT_ID();

INSERT INTO familiares(usuario_id,nome,cpf,telefone,email,parentesco_principal) VALUES (NULL,'Beatriz Almeida','80000009032','11900000090','contato90@example.com','Filha');

INSERT INTO idosos_familiares(idoso_id,familiar_id,parentesco,responsavel_principal) VALUES (@demo_idoso_90,LAST_INSERT_ID(),'Filha',1);

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_90,@demo_saude,'[Demonstração] Participou da atividade de música e convivência. Boa aceitação das refeições.',1,DATE_SUB(NOW(),INTERVAL 15 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_90,@demo_saude,'[Demonstração] Avaliação da equipe: manter acompanhamento e rotina de cuidados individualizada.',0,DATE_SUB(NOW(),INTERVAL 7 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_90,@demo_saude,'[Demonstração] Rotina tranquila, participou da caminhada assistida e recebeu contato da família.',1,DATE_SUB(NOW(),INTERVAL 1 DAY));

INSERT INTO idosos(nome,cpf,identificador,quarto_id,ativo,criado_em) VALUES ('Tereza Almeida','70000009105','DEMO-091',@demo_quarto_23,1,DATE_SUB(NOW(),INTERVAL 89 DAY));

SET @demo_idoso_91 = LAST_INSERT_ID();

INSERT INTO familiares(usuario_id,nome,cpf,telefone,email,parentesco_principal) VALUES (NULL,'Gabriel Almeida','80000009113','11900000091','contato91@example.com','Filha');

INSERT INTO idosos_familiares(idoso_id,familiar_id,parentesco,responsavel_principal) VALUES (@demo_idoso_91,LAST_INSERT_ID(),'Filha',1);

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_91,@demo_saude,'[Demonstração] Participou da atividade de música e convivência. Boa aceitação das refeições.',1,DATE_SUB(NOW(),INTERVAL 16 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_91,@demo_saude,'[Demonstração] Avaliação da equipe: manter acompanhamento e rotina de cuidados individualizada.',0,DATE_SUB(NOW(),INTERVAL 8 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_91,@demo_saude,'[Demonstração] Rotina tranquila, participou da caminhada assistida e recebeu contato da família.',1,DATE_SUB(NOW(),INTERVAL 2 DAY));

INSERT INTO idosos(nome,cpf,identificador,quarto_id,ativo,criado_em) VALUES ('Luiz Almeida','70000009296','DEMO-092',@demo_quarto_23,1,DATE_SUB(NOW(),INTERVAL 88 DAY));

SET @demo_idoso_92 = LAST_INSERT_ID();

INSERT INTO familiares(usuario_id,nome,cpf,telefone,email,parentesco_principal) VALUES (NULL,'Carolina Almeida','80000009202','11900000092','contato92@example.com','Filha');

INSERT INTO idosos_familiares(idoso_id,familiar_id,parentesco,responsavel_principal) VALUES (@demo_idoso_92,LAST_INSERT_ID(),'Filha',1);

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_92,@demo_saude,'[Demonstração] Participou da atividade de música e convivência. Boa aceitação das refeições.',1,DATE_SUB(NOW(),INTERVAL 17 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_92,@demo_saude,'[Demonstração] Avaliação da equipe: manter acompanhamento e rotina de cuidados individualizada.',0,DATE_SUB(NOW(),INTERVAL 9 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_92,@demo_saude,'[Demonstração] Rotina tranquila, participou da caminhada assistida e recebeu contato da família.',1,DATE_SUB(NOW(),INTERVAL 3 DAY));

INSERT INTO idosos(nome,cpf,identificador,quarto_id,ativo,criado_em) VALUES ('Rosa Almeida','70000009377','DEMO-093',@demo_quarto_24,1,DATE_SUB(NOW(),INTERVAL 87 DAY));

SET @demo_idoso_93 = LAST_INSERT_ID();

INSERT INTO familiares(usuario_id,nome,cpf,telefone,email,parentesco_principal) VALUES (NULL,'Rafael Almeida','80000009385','11900000093','contato93@example.com','Filha');

INSERT INTO idosos_familiares(idoso_id,familiar_id,parentesco,responsavel_principal) VALUES (@demo_idoso_93,LAST_INSERT_ID(),'Filha',1);

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_93,@demo_saude,'[Demonstração] Participou da atividade de música e convivência. Boa aceitação das refeições.',1,DATE_SUB(NOW(),INTERVAL 15 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_93,@demo_saude,'[Demonstração] Avaliação da equipe: manter acompanhamento e rotina de cuidados individualizada.',0,DATE_SUB(NOW(),INTERVAL 7 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_93,@demo_saude,'[Demonstração] Rotina tranquila, participou da caminhada assistida e recebeu contato da família.',1,DATE_SUB(NOW(),INTERVAL 1 DAY));

INSERT INTO idosos(nome,cpf,identificador,quarto_id,ativo,criado_em) VALUES ('Paulo Almeida','70000009458','DEMO-094',@demo_quarto_24,1,DATE_SUB(NOW(),INTERVAL 86 DAY));

SET @demo_idoso_94 = LAST_INSERT_ID();

INSERT INTO familiares(usuario_id,nome,cpf,telefone,email,parentesco_principal) VALUES (NULL,'Beatriz Almeida','80000009466','11900000094','contato94@example.com','Filha');

INSERT INTO idosos_familiares(idoso_id,familiar_id,parentesco,responsavel_principal) VALUES (@demo_idoso_94,LAST_INSERT_ID(),'Filha',1);

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_94,@demo_saude,'[Demonstração] Participou da atividade de música e convivência. Boa aceitação das refeições.',1,DATE_SUB(NOW(),INTERVAL 16 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_94,@demo_saude,'[Demonstração] Avaliação da equipe: manter acompanhamento e rotina de cuidados individualizada.',0,DATE_SUB(NOW(),INTERVAL 8 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_94,@demo_saude,'[Demonstração] Rotina tranquila, participou da caminhada assistida e recebeu contato da família.',1,DATE_SUB(NOW(),INTERVAL 2 DAY));

INSERT INTO idosos(nome,cpf,identificador,quarto_id,ativo,criado_em) VALUES ('Célia Almeida','70000009539','DEMO-095',@demo_quarto_24,1,DATE_SUB(NOW(),INTERVAL 85 DAY));

SET @demo_idoso_95 = LAST_INSERT_ID();

INSERT INTO familiares(usuario_id,nome,cpf,telefone,email,parentesco_principal) VALUES (NULL,'Gabriel Almeida','80000009547','11900000095','contato95@example.com','Filha');

INSERT INTO idosos_familiares(idoso_id,familiar_id,parentesco,responsavel_principal) VALUES (@demo_idoso_95,LAST_INSERT_ID(),'Filha',1);

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_95,@demo_saude,'[Demonstração] Participou da atividade de música e convivência. Boa aceitação das refeições.',1,DATE_SUB(NOW(),INTERVAL 17 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_95,@demo_saude,'[Demonstração] Avaliação da equipe: manter acompanhamento e rotina de cuidados individualizada.',0,DATE_SUB(NOW(),INTERVAL 9 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_95,@demo_saude,'[Demonstração] Rotina tranquila, participou da caminhada assistida e recebeu contato da família.',1,DATE_SUB(NOW(),INTERVAL 3 DAY));

INSERT INTO idosos(nome,cpf,identificador,quarto_id,ativo,criado_em) VALUES ('Joaquim Almeida','70000009610','DEMO-096',@demo_quarto_24,1,DATE_SUB(NOW(),INTERVAL 84 DAY));

SET @demo_idoso_96 = LAST_INSERT_ID();

INSERT INTO familiares(usuario_id,nome,cpf,telefone,email,parentesco_principal) VALUES (NULL,'Carolina Almeida','80000009628','11900000096','contato96@example.com','Filha');

INSERT INTO idosos_familiares(idoso_id,familiar_id,parentesco,responsavel_principal) VALUES (@demo_idoso_96,LAST_INSERT_ID(),'Filha',1);

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_96,@demo_saude,'[Demonstração] Participou da atividade de música e convivência. Boa aceitação das refeições.',1,DATE_SUB(NOW(),INTERVAL 15 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_96,@demo_saude,'[Demonstração] Avaliação da equipe: manter acompanhamento e rotina de cuidados individualizada.',0,DATE_SUB(NOW(),INTERVAL 7 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_96,@demo_saude,'[Demonstração] Rotina tranquila, participou da caminhada assistida e recebeu contato da família.',1,DATE_SUB(NOW(),INTERVAL 1 DAY));

INSERT INTO idosos(nome,cpf,identificador,quarto_id,ativo,criado_em) VALUES ('Rita Almeida','70000009709','DEMO-097',@demo_quarto_25,0,DATE_SUB(NOW(),INTERVAL 83 DAY));

SET @demo_idoso_97 = LAST_INSERT_ID();

INSERT INTO familiares(usuario_id,nome,cpf,telefone,email,parentesco_principal) VALUES (NULL,'Rafael Almeida','80000009709','11900000097','contato97@example.com','Filha');

INSERT INTO idosos_familiares(idoso_id,familiar_id,parentesco,responsavel_principal) VALUES (@demo_idoso_97,LAST_INSERT_ID(),'Filha',1);

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_97,@demo_saude,'[Demonstração] Participou da atividade de música e convivência. Boa aceitação das refeições.',1,DATE_SUB(NOW(),INTERVAL 16 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_97,@demo_saude,'[Demonstração] Avaliação da equipe: manter acompanhamento e rotina de cuidados individualizada.',0,DATE_SUB(NOW(),INTERVAL 8 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_97,@demo_saude,'[Demonstração] Rotina tranquila, participou da caminhada assistida e recebeu contato da família.',1,DATE_SUB(NOW(),INTERVAL 2 DAY));

INSERT INTO idosos(nome,cpf,identificador,quarto_id,ativo,criado_em) VALUES ('Sebastião Almeida','70000009881','DEMO-098',@demo_quarto_25,0,DATE_SUB(NOW(),INTERVAL 82 DAY));

SET @demo_idoso_98 = LAST_INSERT_ID();

INSERT INTO familiares(usuario_id,nome,cpf,telefone,email,parentesco_principal) VALUES (NULL,'Beatriz Almeida','80000009890','11900000098','contato98@example.com','Filha');

INSERT INTO idosos_familiares(idoso_id,familiar_id,parentesco,responsavel_principal) VALUES (@demo_idoso_98,LAST_INSERT_ID(),'Filha',1);

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_98,@demo_saude,'[Demonstração] Participou da atividade de música e convivência. Boa aceitação das refeições.',1,DATE_SUB(NOW(),INTERVAL 17 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_98,@demo_saude,'[Demonstração] Avaliação da equipe: manter acompanhamento e rotina de cuidados individualizada.',0,DATE_SUB(NOW(),INTERVAL 9 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_98,@demo_saude,'[Demonstração] Rotina tranquila, participou da caminhada assistida e recebeu contato da família.',1,DATE_SUB(NOW(),INTERVAL 3 DAY));

INSERT INTO idosos(nome,cpf,identificador,quarto_id,ativo,criado_em) VALUES ('Alice Almeida','70000009962','DEMO-099',@demo_quarto_25,0,DATE_SUB(NOW(),INTERVAL 81 DAY));

SET @demo_idoso_99 = LAST_INSERT_ID();

INSERT INTO familiares(usuario_id,nome,cpf,telefone,email,parentesco_principal) VALUES (NULL,'Gabriel Almeida','80000009970','11900000099','contato99@example.com','Filha');

INSERT INTO idosos_familiares(idoso_id,familiar_id,parentesco,responsavel_principal) VALUES (@demo_idoso_99,LAST_INSERT_ID(),'Filha',1);

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_99,@demo_saude,'[Demonstração] Participou da atividade de música e convivência. Boa aceitação das refeições.',1,DATE_SUB(NOW(),INTERVAL 15 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_99,@demo_saude,'[Demonstração] Avaliação da equipe: manter acompanhamento e rotina de cuidados individualizada.',0,DATE_SUB(NOW(),INTERVAL 7 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_99,@demo_saude,'[Demonstração] Rotina tranquila, participou da caminhada assistida e recebeu contato da família.',1,DATE_SUB(NOW(),INTERVAL 1 DAY));

INSERT INTO idosos(nome,cpf,identificador,quarto_id,ativo,criado_em) VALUES ('Francisco Almeida','70000010030','DEMO-100',@demo_quarto_25,0,DATE_SUB(NOW(),INTERVAL 80 DAY));

SET @demo_idoso_100 = LAST_INSERT_ID();

INSERT INTO familiares(usuario_id,nome,cpf,telefone,email,parentesco_principal) VALUES (NULL,'Carolina Almeida','80000010049','11900000100','contato100@example.com','Filha');

INSERT INTO idosos_familiares(idoso_id,familiar_id,parentesco,responsavel_principal) VALUES (@demo_idoso_100,LAST_INSERT_ID(),'Filha',1);

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_100,@demo_saude,'[Demonstração] Participou da atividade de música e convivência. Boa aceitação das refeições.',1,DATE_SUB(NOW(),INTERVAL 16 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_100,@demo_saude,'[Demonstração] Avaliação da equipe: manter acompanhamento e rotina de cuidados individualizada.',0,DATE_SUB(NOW(),INTERVAL 8 DAY));

INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar,registrado_em) VALUES (@demo_idoso_100,@demo_saude,'[Demonstração] Rotina tranquila, participou da caminhada assistida e recebeu contato da família.',1,DATE_SUB(NOW(),INTERVAL 2 DAY));

INSERT INTO materiais(nome,tipo,descricao,unidade_medida,estoque_minimo) VALUES ('Fralda geriátrica G','Insumo','Item fictício para demonstração.','un',100);

SET @demo_material_1 = LAST_INSERT_ID();

INSERT INTO movimentacoes_estoque(material_id,tipo,quantidade,origem,usuario_id,observacao,movimentado_em) VALUES (@demo_material_1,'Entrada',1500,'Plano de Saúde',@demo_almoxarife,'Abastecimento de demonstração',DATE_SUB(NOW(),INTERVAL 30 DAY));

INSERT INTO movimentacoes_estoque(material_id,tipo,quantidade,origem,usuario_id,observacao,movimentado_em) VALUES (@demo_material_1,'Entrada',500,'Plano de Saúde',@demo_almoxarife,'Abastecimento de demonstração',DATE_SUB(NOW(),INTERVAL 10 DAY));

INSERT INTO solicitacoes_compra(material_id,usuario_solicitante_id,quantidade,observacao,solicitada_em) VALUES (@demo_material_1,@demo_almoxarife,500,'Reposição recebida; entrada registrada separadamente.',DATE_SUB(NOW(),INTERVAL 10 DAY));

SET @demo_compra = LAST_INSERT_ID();

SET @asilosoft_usuario_id = @demo_admin;

UPDATE solicitacoes_compra SET situacao='Aprovada' WHERE id=@demo_compra;

UPDATE solicitacoes_compra SET situacao='Atendida' WHERE id=@demo_compra;

SET @asilosoft_usuario_id = @demo_almoxarife;

INSERT INTO solicitacoes_compra(material_id,usuario_solicitante_id,quantidade,observacao,solicitada_em) VALUES (@demo_material_1,@demo_almoxarife,500,'Recusada: quantidade revista após inventário.',DATE_SUB(NOW(),INTERVAL 8 DAY));

SET @demo_compra = LAST_INSERT_ID();

SET @asilosoft_usuario_id = @demo_admin;

UPDATE solicitacoes_compra SET situacao='Recusada' WHERE id=@demo_compra;

SET @asilosoft_usuario_id = @demo_almoxarife;

INSERT INTO solicitacoes_compra(material_id,usuario_solicitante_id,quantidade,observacao,solicitada_em) VALUES (@demo_material_1,@demo_almoxarife,500,'Reposição autorizada; aguardando fornecedor.',DATE_SUB(NOW(),INTERVAL 4 DAY));

SET @demo_compra = LAST_INSERT_ID();

SET @asilosoft_usuario_id = @demo_admin;

UPDATE solicitacoes_compra SET situacao='Aprovada' WHERE id=@demo_compra;

SET @asilosoft_usuario_id = @demo_almoxarife;

INSERT INTO solicitacoes_compra(material_id,usuario_solicitante_id,quantidade,observacao,solicitada_em) VALUES (@demo_material_1,@demo_almoxarife,500,'Solicitação aguardando análise.',DATE_SUB(NOW(),INTERVAL 1 DAY));

INSERT INTO materiais(nome,tipo,descricao,unidade_medida,estoque_minimo) VALUES ('Fralda geriátrica M','Insumo','Item fictício para demonstração.','un',80);

SET @demo_material_2 = LAST_INSERT_ID();

INSERT INTO movimentacoes_estoque(material_id,tipo,quantidade,origem,usuario_id,observacao,movimentado_em) VALUES (@demo_material_2,'Entrada',1200,'Instituição',@demo_almoxarife,'Abastecimento de demonstração',DATE_SUB(NOW(),INTERVAL 30 DAY));

INSERT INTO movimentacoes_estoque(material_id,tipo,quantidade,origem,usuario_id,observacao,movimentado_em) VALUES (@demo_material_2,'Entrada',400,'Instituição',@demo_almoxarife,'Abastecimento de demonstração',DATE_SUB(NOW(),INTERVAL 10 DAY));

INSERT INTO solicitacoes_compra(material_id,usuario_solicitante_id,quantidade,observacao,solicitada_em) VALUES (@demo_material_2,@demo_almoxarife,400,'Reposição recebida; entrada registrada separadamente.',DATE_SUB(NOW(),INTERVAL 10 DAY));

SET @demo_compra = LAST_INSERT_ID();

SET @asilosoft_usuario_id = @demo_admin;

UPDATE solicitacoes_compra SET situacao='Aprovada' WHERE id=@demo_compra;

UPDATE solicitacoes_compra SET situacao='Atendida' WHERE id=@demo_compra;

SET @asilosoft_usuario_id = @demo_almoxarife;

INSERT INTO solicitacoes_compra(material_id,usuario_solicitante_id,quantidade,observacao,solicitada_em) VALUES (@demo_material_2,@demo_almoxarife,400,'Recusada: quantidade revista após inventário.',DATE_SUB(NOW(),INTERVAL 8 DAY));

SET @demo_compra = LAST_INSERT_ID();

SET @asilosoft_usuario_id = @demo_admin;

UPDATE solicitacoes_compra SET situacao='Recusada' WHERE id=@demo_compra;

SET @asilosoft_usuario_id = @demo_almoxarife;

INSERT INTO solicitacoes_compra(material_id,usuario_solicitante_id,quantidade,observacao,solicitada_em) VALUES (@demo_material_2,@demo_almoxarife,400,'Reposição autorizada; aguardando fornecedor.',DATE_SUB(NOW(),INTERVAL 4 DAY));

SET @demo_compra = LAST_INSERT_ID();

SET @asilosoft_usuario_id = @demo_admin;

UPDATE solicitacoes_compra SET situacao='Aprovada' WHERE id=@demo_compra;

SET @asilosoft_usuario_id = @demo_almoxarife;

INSERT INTO solicitacoes_compra(material_id,usuario_solicitante_id,quantidade,observacao,solicitada_em) VALUES (@demo_material_2,@demo_almoxarife,400,'Solicitação aguardando análise.',DATE_SUB(NOW(),INTERVAL 1 DAY));

INSERT INTO materiais(nome,tipo,descricao,unidade_medida,estoque_minimo) VALUES ('Luvas descartáveis','Insumo','Item fictício para demonstração.','cx',15);

SET @demo_material_3 = LAST_INSERT_ID();

INSERT INTO movimentacoes_estoque(material_id,tipo,quantidade,origem,usuario_id,observacao,movimentado_em) VALUES (@demo_material_3,'Entrada',225,'SUS',@demo_almoxarife,'Abastecimento de demonstração',DATE_SUB(NOW(),INTERVAL 30 DAY));

INSERT INTO movimentacoes_estoque(material_id,tipo,quantidade,origem,usuario_id,observacao,movimentado_em) VALUES (@demo_material_3,'Entrada',75,'SUS',@demo_almoxarife,'Abastecimento de demonstração',DATE_SUB(NOW(),INTERVAL 10 DAY));

INSERT INTO solicitacoes_compra(material_id,usuario_solicitante_id,quantidade,observacao,solicitada_em) VALUES (@demo_material_3,@demo_almoxarife,75,'Reposição recebida; entrada registrada separadamente.',DATE_SUB(NOW(),INTERVAL 10 DAY));

SET @demo_compra = LAST_INSERT_ID();

SET @asilosoft_usuario_id = @demo_admin;

UPDATE solicitacoes_compra SET situacao='Aprovada' WHERE id=@demo_compra;

UPDATE solicitacoes_compra SET situacao='Atendida' WHERE id=@demo_compra;

SET @asilosoft_usuario_id = @demo_almoxarife;

INSERT INTO solicitacoes_compra(material_id,usuario_solicitante_id,quantidade,observacao,solicitada_em) VALUES (@demo_material_3,@demo_almoxarife,75,'Recusada: quantidade revista após inventário.',DATE_SUB(NOW(),INTERVAL 8 DAY));

SET @demo_compra = LAST_INSERT_ID();

SET @asilosoft_usuario_id = @demo_admin;

UPDATE solicitacoes_compra SET situacao='Recusada' WHERE id=@demo_compra;

SET @asilosoft_usuario_id = @demo_almoxarife;

INSERT INTO solicitacoes_compra(material_id,usuario_solicitante_id,quantidade,observacao,solicitada_em) VALUES (@demo_material_3,@demo_almoxarife,75,'Reposição autorizada; aguardando fornecedor.',DATE_SUB(NOW(),INTERVAL 4 DAY));

SET @demo_compra = LAST_INSERT_ID();

SET @asilosoft_usuario_id = @demo_admin;

UPDATE solicitacoes_compra SET situacao='Aprovada' WHERE id=@demo_compra;

SET @asilosoft_usuario_id = @demo_almoxarife;

INSERT INTO solicitacoes_compra(material_id,usuario_solicitante_id,quantidade,observacao,solicitada_em) VALUES (@demo_material_3,@demo_almoxarife,75,'Solicitação aguardando análise.',DATE_SUB(NOW(),INTERVAL 1 DAY));

INSERT INTO materiais(nome,tipo,descricao,unidade_medida,estoque_minimo) VALUES ('Gaze estéril','Insumo','Item fictício para demonstração.','pct',30);

SET @demo_material_4 = LAST_INSERT_ID();

INSERT INTO movimentacoes_estoque(material_id,tipo,quantidade,origem,usuario_id,observacao,movimentado_em) VALUES (@demo_material_4,'Entrada',450,'Plano de Saúde',@demo_almoxarife,'Abastecimento de demonstração',DATE_SUB(NOW(),INTERVAL 30 DAY));

INSERT INTO movimentacoes_estoque(material_id,tipo,quantidade,origem,usuario_id,observacao,movimentado_em) VALUES (@demo_material_4,'Entrada',150,'Plano de Saúde',@demo_almoxarife,'Abastecimento de demonstração',DATE_SUB(NOW(),INTERVAL 10 DAY));

INSERT INTO solicitacoes_compra(material_id,usuario_solicitante_id,quantidade,observacao,solicitada_em) VALUES (@demo_material_4,@demo_almoxarife,150,'Reposição recebida; entrada registrada separadamente.',DATE_SUB(NOW(),INTERVAL 10 DAY));

SET @demo_compra = LAST_INSERT_ID();

SET @asilosoft_usuario_id = @demo_admin;

UPDATE solicitacoes_compra SET situacao='Aprovada' WHERE id=@demo_compra;

UPDATE solicitacoes_compra SET situacao='Atendida' WHERE id=@demo_compra;

SET @asilosoft_usuario_id = @demo_almoxarife;

INSERT INTO solicitacoes_compra(material_id,usuario_solicitante_id,quantidade,observacao,solicitada_em) VALUES (@demo_material_4,@demo_almoxarife,150,'Recusada: quantidade revista após inventário.',DATE_SUB(NOW(),INTERVAL 8 DAY));

SET @demo_compra = LAST_INSERT_ID();

SET @asilosoft_usuario_id = @demo_admin;

UPDATE solicitacoes_compra SET situacao='Recusada' WHERE id=@demo_compra;

SET @asilosoft_usuario_id = @demo_almoxarife;

INSERT INTO solicitacoes_compra(material_id,usuario_solicitante_id,quantidade,observacao,solicitada_em) VALUES (@demo_material_4,@demo_almoxarife,150,'Reposição autorizada; aguardando fornecedor.',DATE_SUB(NOW(),INTERVAL 4 DAY));

SET @demo_compra = LAST_INSERT_ID();

SET @asilosoft_usuario_id = @demo_admin;

UPDATE solicitacoes_compra SET situacao='Aprovada' WHERE id=@demo_compra;

SET @asilosoft_usuario_id = @demo_almoxarife;

INSERT INTO solicitacoes_compra(material_id,usuario_solicitante_id,quantidade,observacao,solicitada_em) VALUES (@demo_material_4,@demo_almoxarife,150,'Solicitação aguardando análise.',DATE_SUB(NOW(),INTERVAL 1 DAY));

INSERT INTO materiais(nome,tipo,descricao,unidade_medida,estoque_minimo) VALUES ('Álcool 70%','Insumo','Item fictício para demonstração.','litro',20);

SET @demo_material_5 = LAST_INSERT_ID();

INSERT INTO movimentacoes_estoque(material_id,tipo,quantidade,origem,usuario_id,observacao,movimentado_em) VALUES (@demo_material_5,'Entrada',300,'Instituição',@demo_almoxarife,'Abastecimento de demonstração',DATE_SUB(NOW(),INTERVAL 30 DAY));

INSERT INTO movimentacoes_estoque(material_id,tipo,quantidade,origem,usuario_id,observacao,movimentado_em) VALUES (@demo_material_5,'Entrada',100,'Instituição',@demo_almoxarife,'Abastecimento de demonstração',DATE_SUB(NOW(),INTERVAL 10 DAY));

INSERT INTO solicitacoes_compra(material_id,usuario_solicitante_id,quantidade,observacao,solicitada_em) VALUES (@demo_material_5,@demo_almoxarife,100,'Reposição recebida; entrada registrada separadamente.',DATE_SUB(NOW(),INTERVAL 10 DAY));

SET @demo_compra = LAST_INSERT_ID();

SET @asilosoft_usuario_id = @demo_admin;

UPDATE solicitacoes_compra SET situacao='Aprovada' WHERE id=@demo_compra;

UPDATE solicitacoes_compra SET situacao='Atendida' WHERE id=@demo_compra;

SET @asilosoft_usuario_id = @demo_almoxarife;

INSERT INTO solicitacoes_compra(material_id,usuario_solicitante_id,quantidade,observacao,solicitada_em) VALUES (@demo_material_5,@demo_almoxarife,100,'Recusada: quantidade revista após inventário.',DATE_SUB(NOW(),INTERVAL 8 DAY));

SET @demo_compra = LAST_INSERT_ID();

SET @asilosoft_usuario_id = @demo_admin;

UPDATE solicitacoes_compra SET situacao='Recusada' WHERE id=@demo_compra;

SET @asilosoft_usuario_id = @demo_almoxarife;

INSERT INTO solicitacoes_compra(material_id,usuario_solicitante_id,quantidade,observacao,solicitada_em) VALUES (@demo_material_5,@demo_almoxarife,100,'Reposição autorizada; aguardando fornecedor.',DATE_SUB(NOW(),INTERVAL 4 DAY));

SET @demo_compra = LAST_INSERT_ID();

SET @asilosoft_usuario_id = @demo_admin;

UPDATE solicitacoes_compra SET situacao='Aprovada' WHERE id=@demo_compra;

SET @asilosoft_usuario_id = @demo_almoxarife;

INSERT INTO solicitacoes_compra(material_id,usuario_solicitante_id,quantidade,observacao,solicitada_em) VALUES (@demo_material_5,@demo_almoxarife,100,'Solicitação aguardando análise.',DATE_SUB(NOW(),INTERVAL 1 DAY));

INSERT INTO materiais(nome,tipo,descricao,unidade_medida,estoque_minimo) VALUES ('Sabonete líquido','Insumo','Item fictício para demonstração.','litro',15);

SET @demo_material_6 = LAST_INSERT_ID();

INSERT INTO movimentacoes_estoque(material_id,tipo,quantidade,origem,usuario_id,observacao,movimentado_em) VALUES (@demo_material_6,'Entrada',225,'SUS',@demo_almoxarife,'Abastecimento de demonstração',DATE_SUB(NOW(),INTERVAL 30 DAY));

INSERT INTO movimentacoes_estoque(material_id,tipo,quantidade,origem,usuario_id,observacao,movimentado_em) VALUES (@demo_material_6,'Entrada',75,'SUS',@demo_almoxarife,'Abastecimento de demonstração',DATE_SUB(NOW(),INTERVAL 10 DAY));

INSERT INTO solicitacoes_compra(material_id,usuario_solicitante_id,quantidade,observacao,solicitada_em) VALUES (@demo_material_6,@demo_almoxarife,75,'Reposição recebida; entrada registrada separadamente.',DATE_SUB(NOW(),INTERVAL 10 DAY));

SET @demo_compra = LAST_INSERT_ID();

SET @asilosoft_usuario_id = @demo_admin;

UPDATE solicitacoes_compra SET situacao='Aprovada' WHERE id=@demo_compra;

UPDATE solicitacoes_compra SET situacao='Atendida' WHERE id=@demo_compra;

SET @asilosoft_usuario_id = @demo_almoxarife;

INSERT INTO solicitacoes_compra(material_id,usuario_solicitante_id,quantidade,observacao,solicitada_em) VALUES (@demo_material_6,@demo_almoxarife,75,'Recusada: quantidade revista após inventário.',DATE_SUB(NOW(),INTERVAL 8 DAY));

SET @demo_compra = LAST_INSERT_ID();

SET @asilosoft_usuario_id = @demo_admin;

UPDATE solicitacoes_compra SET situacao='Recusada' WHERE id=@demo_compra;

SET @asilosoft_usuario_id = @demo_almoxarife;

INSERT INTO solicitacoes_compra(material_id,usuario_solicitante_id,quantidade,observacao,solicitada_em) VALUES (@demo_material_6,@demo_almoxarife,75,'Reposição autorizada; aguardando fornecedor.',DATE_SUB(NOW(),INTERVAL 4 DAY));

SET @demo_compra = LAST_INSERT_ID();

SET @asilosoft_usuario_id = @demo_admin;

UPDATE solicitacoes_compra SET situacao='Aprovada' WHERE id=@demo_compra;

SET @asilosoft_usuario_id = @demo_almoxarife;

INSERT INTO solicitacoes_compra(material_id,usuario_solicitante_id,quantidade,observacao,solicitada_em) VALUES (@demo_material_6,@demo_almoxarife,75,'Solicitação aguardando análise.',DATE_SUB(NOW(),INTERVAL 1 DAY));

INSERT INTO materiais(nome,tipo,descricao,unidade_medida,estoque_minimo) VALUES ('Papel toalha','Material','Item fictício para demonstração.','pct',20);

SET @demo_material_7 = LAST_INSERT_ID();

INSERT INTO movimentacoes_estoque(material_id,tipo,quantidade,origem,usuario_id,observacao,movimentado_em) VALUES (@demo_material_7,'Entrada',300,'Plano de Saúde',@demo_almoxarife,'Abastecimento de demonstração',DATE_SUB(NOW(),INTERVAL 30 DAY));

INSERT INTO movimentacoes_estoque(material_id,tipo,quantidade,origem,usuario_id,observacao,movimentado_em) VALUES (@demo_material_7,'Entrada',100,'Plano de Saúde',@demo_almoxarife,'Abastecimento de demonstração',DATE_SUB(NOW(),INTERVAL 10 DAY));

INSERT INTO solicitacoes_compra(material_id,usuario_solicitante_id,quantidade,observacao,solicitada_em) VALUES (@demo_material_7,@demo_almoxarife,100,'Reposição recebida; entrada registrada separadamente.',DATE_SUB(NOW(),INTERVAL 10 DAY));

SET @demo_compra = LAST_INSERT_ID();

SET @asilosoft_usuario_id = @demo_admin;

UPDATE solicitacoes_compra SET situacao='Aprovada' WHERE id=@demo_compra;

UPDATE solicitacoes_compra SET situacao='Atendida' WHERE id=@demo_compra;

SET @asilosoft_usuario_id = @demo_almoxarife;

INSERT INTO solicitacoes_compra(material_id,usuario_solicitante_id,quantidade,observacao,solicitada_em) VALUES (@demo_material_7,@demo_almoxarife,100,'Recusada: quantidade revista após inventário.',DATE_SUB(NOW(),INTERVAL 8 DAY));

SET @demo_compra = LAST_INSERT_ID();

SET @asilosoft_usuario_id = @demo_admin;

UPDATE solicitacoes_compra SET situacao='Recusada' WHERE id=@demo_compra;

SET @asilosoft_usuario_id = @demo_almoxarife;

INSERT INTO solicitacoes_compra(material_id,usuario_solicitante_id,quantidade,observacao,solicitada_em) VALUES (@demo_material_7,@demo_almoxarife,100,'Reposição autorizada; aguardando fornecedor.',DATE_SUB(NOW(),INTERVAL 4 DAY));

SET @demo_compra = LAST_INSERT_ID();

SET @asilosoft_usuario_id = @demo_admin;

UPDATE solicitacoes_compra SET situacao='Aprovada' WHERE id=@demo_compra;

SET @asilosoft_usuario_id = @demo_almoxarife;

INSERT INTO solicitacoes_compra(material_id,usuario_solicitante_id,quantidade,observacao,solicitada_em) VALUES (@demo_material_7,@demo_almoxarife,100,'Solicitação aguardando análise.',DATE_SUB(NOW(),INTERVAL 1 DAY));

INSERT INTO materiais(nome,tipo,descricao,unidade_medida,estoque_minimo) VALUES ('Roupa de cama','Material','Item fictício para demonstração.','jogo',10);

SET @demo_material_8 = LAST_INSERT_ID();

INSERT INTO movimentacoes_estoque(material_id,tipo,quantidade,origem,usuario_id,observacao,movimentado_em) VALUES (@demo_material_8,'Entrada',150,'Instituição',@demo_almoxarife,'Abastecimento de demonstração',DATE_SUB(NOW(),INTERVAL 30 DAY));

INSERT INTO movimentacoes_estoque(material_id,tipo,quantidade,origem,usuario_id,observacao,movimentado_em) VALUES (@demo_material_8,'Entrada',50,'Instituição',@demo_almoxarife,'Abastecimento de demonstração',DATE_SUB(NOW(),INTERVAL 10 DAY));

INSERT INTO solicitacoes_compra(material_id,usuario_solicitante_id,quantidade,observacao,solicitada_em) VALUES (@demo_material_8,@demo_almoxarife,50,'Reposição recebida; entrada registrada separadamente.',DATE_SUB(NOW(),INTERVAL 10 DAY));

SET @demo_compra = LAST_INSERT_ID();

SET @asilosoft_usuario_id = @demo_admin;

UPDATE solicitacoes_compra SET situacao='Aprovada' WHERE id=@demo_compra;

UPDATE solicitacoes_compra SET situacao='Atendida' WHERE id=@demo_compra;

SET @asilosoft_usuario_id = @demo_almoxarife;

INSERT INTO solicitacoes_compra(material_id,usuario_solicitante_id,quantidade,observacao,solicitada_em) VALUES (@demo_material_8,@demo_almoxarife,50,'Recusada: quantidade revista após inventário.',DATE_SUB(NOW(),INTERVAL 8 DAY));

SET @demo_compra = LAST_INSERT_ID();

SET @asilosoft_usuario_id = @demo_admin;

UPDATE solicitacoes_compra SET situacao='Recusada' WHERE id=@demo_compra;

SET @asilosoft_usuario_id = @demo_almoxarife;

INSERT INTO solicitacoes_compra(material_id,usuario_solicitante_id,quantidade,observacao,solicitada_em) VALUES (@demo_material_8,@demo_almoxarife,50,'Reposição autorizada; aguardando fornecedor.',DATE_SUB(NOW(),INTERVAL 4 DAY));

SET @demo_compra = LAST_INSERT_ID();

SET @asilosoft_usuario_id = @demo_admin;

UPDATE solicitacoes_compra SET situacao='Aprovada' WHERE id=@demo_compra;

SET @asilosoft_usuario_id = @demo_almoxarife;

INSERT INTO solicitacoes_compra(material_id,usuario_solicitante_id,quantidade,observacao,solicitada_em) VALUES (@demo_material_8,@demo_almoxarife,50,'Solicitação aguardando análise.',DATE_SUB(NOW(),INTERVAL 1 DAY));

INSERT INTO materiais(nome,tipo,descricao,unidade_medida,estoque_minimo) VALUES ('Toalha de banho','Material','Item fictício para demonstração.','un',15);

SET @demo_material_9 = LAST_INSERT_ID();

INSERT INTO movimentacoes_estoque(material_id,tipo,quantidade,origem,usuario_id,observacao,movimentado_em) VALUES (@demo_material_9,'Entrada',225,'SUS',@demo_almoxarife,'Abastecimento de demonstração',DATE_SUB(NOW(),INTERVAL 30 DAY));

INSERT INTO movimentacoes_estoque(material_id,tipo,quantidade,origem,usuario_id,observacao,movimentado_em) VALUES (@demo_material_9,'Entrada',75,'SUS',@demo_almoxarife,'Abastecimento de demonstração',DATE_SUB(NOW(),INTERVAL 10 DAY));

INSERT INTO solicitacoes_compra(material_id,usuario_solicitante_id,quantidade,observacao,solicitada_em) VALUES (@demo_material_9,@demo_almoxarife,75,'Reposição recebida; entrada registrada separadamente.',DATE_SUB(NOW(),INTERVAL 10 DAY));

SET @demo_compra = LAST_INSERT_ID();

SET @asilosoft_usuario_id = @demo_admin;

UPDATE solicitacoes_compra SET situacao='Aprovada' WHERE id=@demo_compra;

UPDATE solicitacoes_compra SET situacao='Atendida' WHERE id=@demo_compra;

SET @asilosoft_usuario_id = @demo_almoxarife;

INSERT INTO solicitacoes_compra(material_id,usuario_solicitante_id,quantidade,observacao,solicitada_em) VALUES (@demo_material_9,@demo_almoxarife,75,'Recusada: quantidade revista após inventário.',DATE_SUB(NOW(),INTERVAL 8 DAY));

SET @demo_compra = LAST_INSERT_ID();

SET @asilosoft_usuario_id = @demo_admin;

UPDATE solicitacoes_compra SET situacao='Recusada' WHERE id=@demo_compra;

SET @asilosoft_usuario_id = @demo_almoxarife;

INSERT INTO solicitacoes_compra(material_id,usuario_solicitante_id,quantidade,observacao,solicitada_em) VALUES (@demo_material_9,@demo_almoxarife,75,'Reposição autorizada; aguardando fornecedor.',DATE_SUB(NOW(),INTERVAL 4 DAY));

SET @demo_compra = LAST_INSERT_ID();

SET @asilosoft_usuario_id = @demo_admin;

UPDATE solicitacoes_compra SET situacao='Aprovada' WHERE id=@demo_compra;

SET @asilosoft_usuario_id = @demo_almoxarife;

INSERT INTO solicitacoes_compra(material_id,usuario_solicitante_id,quantidade,observacao,solicitada_em) VALUES (@demo_material_9,@demo_almoxarife,75,'Solicitação aguardando análise.',DATE_SUB(NOW(),INTERVAL 1 DAY));

INSERT INTO materiais(nome,tipo,descricao,unidade_medida,estoque_minimo) VALUES ('Cadeira de rodas','Material','Item fictício para demonstração.','un',2);

SET @demo_material_10 = LAST_INSERT_ID();

INSERT INTO movimentacoes_estoque(material_id,tipo,quantidade,origem,usuario_id,observacao,movimentado_em) VALUES (@demo_material_10,'Entrada',30,'Plano de Saúde',@demo_almoxarife,'Abastecimento de demonstração',DATE_SUB(NOW(),INTERVAL 30 DAY));

INSERT INTO movimentacoes_estoque(material_id,tipo,quantidade,origem,usuario_id,observacao,movimentado_em) VALUES (@demo_material_10,'Entrada',10,'Plano de Saúde',@demo_almoxarife,'Abastecimento de demonstração',DATE_SUB(NOW(),INTERVAL 10 DAY));

INSERT INTO solicitacoes_compra(material_id,usuario_solicitante_id,quantidade,observacao,solicitada_em) VALUES (@demo_material_10,@demo_almoxarife,10,'Reposição recebida; entrada registrada separadamente.',DATE_SUB(NOW(),INTERVAL 10 DAY));

SET @demo_compra = LAST_INSERT_ID();

SET @asilosoft_usuario_id = @demo_admin;

UPDATE solicitacoes_compra SET situacao='Aprovada' WHERE id=@demo_compra;

UPDATE solicitacoes_compra SET situacao='Atendida' WHERE id=@demo_compra;

SET @asilosoft_usuario_id = @demo_almoxarife;

INSERT INTO solicitacoes_compra(material_id,usuario_solicitante_id,quantidade,observacao,solicitada_em) VALUES (@demo_material_10,@demo_almoxarife,10,'Recusada: quantidade revista após inventário.',DATE_SUB(NOW(),INTERVAL 8 DAY));

SET @demo_compra = LAST_INSERT_ID();

SET @asilosoft_usuario_id = @demo_admin;

UPDATE solicitacoes_compra SET situacao='Recusada' WHERE id=@demo_compra;

SET @asilosoft_usuario_id = @demo_almoxarife;

INSERT INTO solicitacoes_compra(material_id,usuario_solicitante_id,quantidade,observacao,solicitada_em) VALUES (@demo_material_10,@demo_almoxarife,10,'Reposição autorizada; aguardando fornecedor.',DATE_SUB(NOW(),INTERVAL 4 DAY));

SET @demo_compra = LAST_INSERT_ID();

SET @asilosoft_usuario_id = @demo_admin;

UPDATE solicitacoes_compra SET situacao='Aprovada' WHERE id=@demo_compra;

SET @asilosoft_usuario_id = @demo_almoxarife;

INSERT INTO solicitacoes_compra(material_id,usuario_solicitante_id,quantidade,observacao,solicitada_em) VALUES (@demo_material_10,@demo_almoxarife,10,'Solicitação aguardando análise.',DATE_SUB(NOW(),INTERVAL 1 DAY));

INSERT INTO materiais(nome,tipo,descricao,unidade_medida,estoque_minimo) VALUES ('Termômetro digital','Material','Item fictício para demonstração.','un',5);

SET @demo_material_11 = LAST_INSERT_ID();

INSERT INTO movimentacoes_estoque(material_id,tipo,quantidade,origem,usuario_id,observacao,movimentado_em) VALUES (@demo_material_11,'Entrada',75,'Instituição',@demo_almoxarife,'Abastecimento de demonstração',DATE_SUB(NOW(),INTERVAL 30 DAY));

INSERT INTO movimentacoes_estoque(material_id,tipo,quantidade,origem,usuario_id,observacao,movimentado_em) VALUES (@demo_material_11,'Entrada',25,'Instituição',@demo_almoxarife,'Abastecimento de demonstração',DATE_SUB(NOW(),INTERVAL 10 DAY));

INSERT INTO solicitacoes_compra(material_id,usuario_solicitante_id,quantidade,observacao,solicitada_em) VALUES (@demo_material_11,@demo_almoxarife,25,'Reposição recebida; entrada registrada separadamente.',DATE_SUB(NOW(),INTERVAL 10 DAY));

SET @demo_compra = LAST_INSERT_ID();

SET @asilosoft_usuario_id = @demo_admin;

UPDATE solicitacoes_compra SET situacao='Aprovada' WHERE id=@demo_compra;

UPDATE solicitacoes_compra SET situacao='Atendida' WHERE id=@demo_compra;

SET @asilosoft_usuario_id = @demo_almoxarife;

INSERT INTO solicitacoes_compra(material_id,usuario_solicitante_id,quantidade,observacao,solicitada_em) VALUES (@demo_material_11,@demo_almoxarife,25,'Recusada: quantidade revista após inventário.',DATE_SUB(NOW(),INTERVAL 8 DAY));

SET @demo_compra = LAST_INSERT_ID();

SET @asilosoft_usuario_id = @demo_admin;

UPDATE solicitacoes_compra SET situacao='Recusada' WHERE id=@demo_compra;

SET @asilosoft_usuario_id = @demo_almoxarife;

INSERT INTO solicitacoes_compra(material_id,usuario_solicitante_id,quantidade,observacao,solicitada_em) VALUES (@demo_material_11,@demo_almoxarife,25,'Reposição autorizada; aguardando fornecedor.',DATE_SUB(NOW(),INTERVAL 4 DAY));

SET @demo_compra = LAST_INSERT_ID();

SET @asilosoft_usuario_id = @demo_admin;

UPDATE solicitacoes_compra SET situacao='Aprovada' WHERE id=@demo_compra;

SET @asilosoft_usuario_id = @demo_almoxarife;

INSERT INTO solicitacoes_compra(material_id,usuario_solicitante_id,quantidade,observacao,solicitada_em) VALUES (@demo_material_11,@demo_almoxarife,25,'Solicitação aguardando análise.',DATE_SUB(NOW(),INTERVAL 1 DAY));

INSERT INTO materiais(nome,tipo,descricao,unidade_medida,estoque_minimo) VALUES ('Máscara descartável','Insumo','Item fictício para demonstração.','cx',10);

SET @demo_material_12 = LAST_INSERT_ID();

INSERT INTO movimentacoes_estoque(material_id,tipo,quantidade,origem,usuario_id,observacao,movimentado_em) VALUES (@demo_material_12,'Entrada',150,'SUS',@demo_almoxarife,'Abastecimento de demonstração',DATE_SUB(NOW(),INTERVAL 30 DAY));

INSERT INTO movimentacoes_estoque(material_id,tipo,quantidade,origem,usuario_id,observacao,movimentado_em) VALUES (@demo_material_12,'Entrada',50,'SUS',@demo_almoxarife,'Abastecimento de demonstração',DATE_SUB(NOW(),INTERVAL 10 DAY));

INSERT INTO solicitacoes_compra(material_id,usuario_solicitante_id,quantidade,observacao,solicitada_em) VALUES (@demo_material_12,@demo_almoxarife,50,'Reposição recebida; entrada registrada separadamente.',DATE_SUB(NOW(),INTERVAL 10 DAY));

SET @demo_compra = LAST_INSERT_ID();

SET @asilosoft_usuario_id = @demo_admin;

UPDATE solicitacoes_compra SET situacao='Aprovada' WHERE id=@demo_compra;

UPDATE solicitacoes_compra SET situacao='Atendida' WHERE id=@demo_compra;

SET @asilosoft_usuario_id = @demo_almoxarife;

INSERT INTO solicitacoes_compra(material_id,usuario_solicitante_id,quantidade,observacao,solicitada_em) VALUES (@demo_material_12,@demo_almoxarife,50,'Recusada: quantidade revista após inventário.',DATE_SUB(NOW(),INTERVAL 8 DAY));

SET @demo_compra = LAST_INSERT_ID();

SET @asilosoft_usuario_id = @demo_admin;

UPDATE solicitacoes_compra SET situacao='Recusada' WHERE id=@demo_compra;

SET @asilosoft_usuario_id = @demo_almoxarife;

INSERT INTO solicitacoes_compra(material_id,usuario_solicitante_id,quantidade,observacao,solicitada_em) VALUES (@demo_material_12,@demo_almoxarife,50,'Reposição autorizada; aguardando fornecedor.',DATE_SUB(NOW(),INTERVAL 4 DAY));

SET @demo_compra = LAST_INSERT_ID();

SET @asilosoft_usuario_id = @demo_admin;

UPDATE solicitacoes_compra SET situacao='Aprovada' WHERE id=@demo_compra;

SET @asilosoft_usuario_id = @demo_almoxarife;

INSERT INTO solicitacoes_compra(material_id,usuario_solicitante_id,quantidade,observacao,solicitada_em) VALUES (@demo_material_12,@demo_almoxarife,50,'Solicitação aguardando análise.',DATE_SUB(NOW(),INTERVAL 1 DAY));

INSERT INTO materiais(nome,tipo,descricao,unidade_medida,estoque_minimo) VALUES ('Soro fisiológico','Medicamento','Item fictício para demonstração.','frasco',30);

SET @demo_material_13 = LAST_INSERT_ID();

INSERT INTO movimentacoes_estoque(material_id,tipo,quantidade,origem,usuario_id,observacao,movimentado_em) VALUES (@demo_material_13,'Entrada',450,'Plano de Saúde',@demo_almoxarife,'Abastecimento de demonstração',DATE_SUB(NOW(),INTERVAL 30 DAY));

INSERT INTO movimentacoes_estoque(material_id,tipo,quantidade,origem,usuario_id,observacao,movimentado_em) VALUES (@demo_material_13,'Entrada',150,'Plano de Saúde',@demo_almoxarife,'Abastecimento de demonstração',DATE_SUB(NOW(),INTERVAL 10 DAY));

INSERT INTO solicitacoes_compra(material_id,usuario_solicitante_id,quantidade,observacao,solicitada_em) VALUES (@demo_material_13,@demo_almoxarife,150,'Reposição recebida; entrada registrada separadamente.',DATE_SUB(NOW(),INTERVAL 10 DAY));

SET @demo_compra = LAST_INSERT_ID();

SET @asilosoft_usuario_id = @demo_admin;

UPDATE solicitacoes_compra SET situacao='Aprovada' WHERE id=@demo_compra;

UPDATE solicitacoes_compra SET situacao='Atendida' WHERE id=@demo_compra;

SET @asilosoft_usuario_id = @demo_almoxarife;

INSERT INTO solicitacoes_compra(material_id,usuario_solicitante_id,quantidade,observacao,solicitada_em) VALUES (@demo_material_13,@demo_almoxarife,150,'Recusada: quantidade revista após inventário.',DATE_SUB(NOW(),INTERVAL 8 DAY));

SET @demo_compra = LAST_INSERT_ID();

SET @asilosoft_usuario_id = @demo_admin;

UPDATE solicitacoes_compra SET situacao='Recusada' WHERE id=@demo_compra;

SET @asilosoft_usuario_id = @demo_almoxarife;

INSERT INTO solicitacoes_compra(material_id,usuario_solicitante_id,quantidade,observacao,solicitada_em) VALUES (@demo_material_13,@demo_almoxarife,150,'Reposição autorizada; aguardando fornecedor.',DATE_SUB(NOW(),INTERVAL 4 DAY));

SET @demo_compra = LAST_INSERT_ID();

SET @asilosoft_usuario_id = @demo_admin;

UPDATE solicitacoes_compra SET situacao='Aprovada' WHERE id=@demo_compra;

SET @asilosoft_usuario_id = @demo_almoxarife;

INSERT INTO solicitacoes_compra(material_id,usuario_solicitante_id,quantidade,observacao,solicitada_em) VALUES (@demo_material_13,@demo_almoxarife,150,'Solicitação aguardando análise.',DATE_SUB(NOW(),INTERVAL 1 DAY));

INSERT INTO materiais(nome,tipo,descricao,unidade_medida,estoque_minimo) VALUES ('Paracetamol 500 mg','Medicamento','Item fictício para demonstração.','cx',10);

SET @demo_material_14 = LAST_INSERT_ID();

INSERT INTO movimentacoes_estoque(material_id,tipo,quantidade,origem,usuario_id,observacao,movimentado_em) VALUES (@demo_material_14,'Entrada',150,'Instituição',@demo_almoxarife,'Abastecimento de demonstração',DATE_SUB(NOW(),INTERVAL 30 DAY));

INSERT INTO movimentacoes_estoque(material_id,tipo,quantidade,origem,usuario_id,observacao,movimentado_em) VALUES (@demo_material_14,'Entrada',50,'Instituição',@demo_almoxarife,'Abastecimento de demonstração',DATE_SUB(NOW(),INTERVAL 10 DAY));

INSERT INTO solicitacoes_compra(material_id,usuario_solicitante_id,quantidade,observacao,solicitada_em) VALUES (@demo_material_14,@demo_almoxarife,50,'Reposição recebida; entrada registrada separadamente.',DATE_SUB(NOW(),INTERVAL 10 DAY));

SET @demo_compra = LAST_INSERT_ID();

SET @asilosoft_usuario_id = @demo_admin;

UPDATE solicitacoes_compra SET situacao='Aprovada' WHERE id=@demo_compra;

UPDATE solicitacoes_compra SET situacao='Atendida' WHERE id=@demo_compra;

SET @asilosoft_usuario_id = @demo_almoxarife;

INSERT INTO solicitacoes_compra(material_id,usuario_solicitante_id,quantidade,observacao,solicitada_em) VALUES (@demo_material_14,@demo_almoxarife,50,'Recusada: quantidade revista após inventário.',DATE_SUB(NOW(),INTERVAL 8 DAY));

SET @demo_compra = LAST_INSERT_ID();

SET @asilosoft_usuario_id = @demo_admin;

UPDATE solicitacoes_compra SET situacao='Recusada' WHERE id=@demo_compra;

SET @asilosoft_usuario_id = @demo_almoxarife;

INSERT INTO solicitacoes_compra(material_id,usuario_solicitante_id,quantidade,observacao,solicitada_em) VALUES (@demo_material_14,@demo_almoxarife,50,'Reposição autorizada; aguardando fornecedor.',DATE_SUB(NOW(),INTERVAL 4 DAY));

SET @demo_compra = LAST_INSERT_ID();

SET @asilosoft_usuario_id = @demo_admin;

UPDATE solicitacoes_compra SET situacao='Aprovada' WHERE id=@demo_compra;

SET @asilosoft_usuario_id = @demo_almoxarife;

INSERT INTO solicitacoes_compra(material_id,usuario_solicitante_id,quantidade,observacao,solicitada_em) VALUES (@demo_material_14,@demo_almoxarife,50,'Solicitação aguardando análise.',DATE_SUB(NOW(),INTERVAL 1 DAY));

INSERT INTO materiais(nome,tipo,descricao,unidade_medida,estoque_minimo) VALUES ('Curativo adesivo','Insumo','Item fictício para demonstração.','cx',15);

SET @demo_material_15 = LAST_INSERT_ID();

INSERT INTO movimentacoes_estoque(material_id,tipo,quantidade,origem,usuario_id,observacao,movimentado_em) VALUES (@demo_material_15,'Entrada',225,'SUS',@demo_almoxarife,'Abastecimento de demonstração',DATE_SUB(NOW(),INTERVAL 30 DAY));

INSERT INTO movimentacoes_estoque(material_id,tipo,quantidade,origem,usuario_id,observacao,movimentado_em) VALUES (@demo_material_15,'Entrada',75,'SUS',@demo_almoxarife,'Abastecimento de demonstração',DATE_SUB(NOW(),INTERVAL 10 DAY));

INSERT INTO solicitacoes_compra(material_id,usuario_solicitante_id,quantidade,observacao,solicitada_em) VALUES (@demo_material_15,@demo_almoxarife,75,'Reposição recebida; entrada registrada separadamente.',DATE_SUB(NOW(),INTERVAL 10 DAY));

SET @demo_compra = LAST_INSERT_ID();

SET @asilosoft_usuario_id = @demo_admin;

UPDATE solicitacoes_compra SET situacao='Aprovada' WHERE id=@demo_compra;

UPDATE solicitacoes_compra SET situacao='Atendida' WHERE id=@demo_compra;

SET @asilosoft_usuario_id = @demo_almoxarife;

INSERT INTO solicitacoes_compra(material_id,usuario_solicitante_id,quantidade,observacao,solicitada_em) VALUES (@demo_material_15,@demo_almoxarife,75,'Recusada: quantidade revista após inventário.',DATE_SUB(NOW(),INTERVAL 8 DAY));

SET @demo_compra = LAST_INSERT_ID();

SET @asilosoft_usuario_id = @demo_admin;

UPDATE solicitacoes_compra SET situacao='Recusada' WHERE id=@demo_compra;

SET @asilosoft_usuario_id = @demo_almoxarife;

INSERT INTO solicitacoes_compra(material_id,usuario_solicitante_id,quantidade,observacao,solicitada_em) VALUES (@demo_material_15,@demo_almoxarife,75,'Reposição autorizada; aguardando fornecedor.',DATE_SUB(NOW(),INTERVAL 4 DAY));

SET @demo_compra = LAST_INSERT_ID();

SET @asilosoft_usuario_id = @demo_admin;

UPDATE solicitacoes_compra SET situacao='Aprovada' WHERE id=@demo_compra;

SET @asilosoft_usuario_id = @demo_almoxarife;

INSERT INTO solicitacoes_compra(material_id,usuario_solicitante_id,quantidade,observacao,solicitada_em) VALUES (@demo_material_15,@demo_almoxarife,75,'Solicitação aguardando análise.',DATE_SUB(NOW(),INTERVAL 1 DAY));

INSERT INTO materiais(nome,tipo,descricao,unidade_medida,estoque_minimo) VALUES ('Creme hidratante','Insumo','Item fictício para demonstração.','frasco',20);

SET @demo_material_16 = LAST_INSERT_ID();

INSERT INTO movimentacoes_estoque(material_id,tipo,quantidade,origem,usuario_id,observacao,movimentado_em) VALUES (@demo_material_16,'Entrada',300,'Plano de Saúde',@demo_almoxarife,'Abastecimento de demonstração',DATE_SUB(NOW(),INTERVAL 30 DAY));

INSERT INTO movimentacoes_estoque(material_id,tipo,quantidade,origem,usuario_id,observacao,movimentado_em) VALUES (@demo_material_16,'Entrada',100,'Plano de Saúde',@demo_almoxarife,'Abastecimento de demonstração',DATE_SUB(NOW(),INTERVAL 10 DAY));

INSERT INTO solicitacoes_compra(material_id,usuario_solicitante_id,quantidade,observacao,solicitada_em) VALUES (@demo_material_16,@demo_almoxarife,100,'Reposição recebida; entrada registrada separadamente.',DATE_SUB(NOW(),INTERVAL 10 DAY));

SET @demo_compra = LAST_INSERT_ID();

SET @asilosoft_usuario_id = @demo_admin;

UPDATE solicitacoes_compra SET situacao='Aprovada' WHERE id=@demo_compra;

UPDATE solicitacoes_compra SET situacao='Atendida' WHERE id=@demo_compra;

SET @asilosoft_usuario_id = @demo_almoxarife;

INSERT INTO solicitacoes_compra(material_id,usuario_solicitante_id,quantidade,observacao,solicitada_em) VALUES (@demo_material_16,@demo_almoxarife,100,'Recusada: quantidade revista após inventário.',DATE_SUB(NOW(),INTERVAL 8 DAY));

SET @demo_compra = LAST_INSERT_ID();

SET @asilosoft_usuario_id = @demo_admin;

UPDATE solicitacoes_compra SET situacao='Recusada' WHERE id=@demo_compra;

SET @asilosoft_usuario_id = @demo_almoxarife;

INSERT INTO solicitacoes_compra(material_id,usuario_solicitante_id,quantidade,observacao,solicitada_em) VALUES (@demo_material_16,@demo_almoxarife,100,'Reposição autorizada; aguardando fornecedor.',DATE_SUB(NOW(),INTERVAL 4 DAY));

SET @demo_compra = LAST_INSERT_ID();

SET @asilosoft_usuario_id = @demo_admin;

UPDATE solicitacoes_compra SET situacao='Aprovada' WHERE id=@demo_compra;

SET @asilosoft_usuario_id = @demo_almoxarife;

INSERT INTO solicitacoes_compra(material_id,usuario_solicitante_id,quantidade,observacao,solicitada_em) VALUES (@demo_material_16,@demo_almoxarife,100,'Solicitação aguardando análise.',DATE_SUB(NOW(),INTERVAL 1 DAY));

INSERT INTO materiais(nome,tipo,descricao,unidade_medida,estoque_minimo) VALUES ('Copo descartável','Material','Item fictício para demonstração.','pct',20);

SET @demo_material_17 = LAST_INSERT_ID();

INSERT INTO movimentacoes_estoque(material_id,tipo,quantidade,origem,usuario_id,observacao,movimentado_em) VALUES (@demo_material_17,'Entrada',300,'Instituição',@demo_almoxarife,'Abastecimento de demonstração',DATE_SUB(NOW(),INTERVAL 30 DAY));

INSERT INTO movimentacoes_estoque(material_id,tipo,quantidade,origem,usuario_id,observacao,movimentado_em) VALUES (@demo_material_17,'Entrada',100,'Instituição',@demo_almoxarife,'Abastecimento de demonstração',DATE_SUB(NOW(),INTERVAL 10 DAY));

INSERT INTO solicitacoes_compra(material_id,usuario_solicitante_id,quantidade,observacao,solicitada_em) VALUES (@demo_material_17,@demo_almoxarife,100,'Reposição recebida; entrada registrada separadamente.',DATE_SUB(NOW(),INTERVAL 10 DAY));

SET @demo_compra = LAST_INSERT_ID();

SET @asilosoft_usuario_id = @demo_admin;

UPDATE solicitacoes_compra SET situacao='Aprovada' WHERE id=@demo_compra;

UPDATE solicitacoes_compra SET situacao='Atendida' WHERE id=@demo_compra;

SET @asilosoft_usuario_id = @demo_almoxarife;

INSERT INTO solicitacoes_compra(material_id,usuario_solicitante_id,quantidade,observacao,solicitada_em) VALUES (@demo_material_17,@demo_almoxarife,100,'Recusada: quantidade revista após inventário.',DATE_SUB(NOW(),INTERVAL 8 DAY));

SET @demo_compra = LAST_INSERT_ID();

SET @asilosoft_usuario_id = @demo_admin;

UPDATE solicitacoes_compra SET situacao='Recusada' WHERE id=@demo_compra;

SET @asilosoft_usuario_id = @demo_almoxarife;

INSERT INTO solicitacoes_compra(material_id,usuario_solicitante_id,quantidade,observacao,solicitada_em) VALUES (@demo_material_17,@demo_almoxarife,100,'Reposição autorizada; aguardando fornecedor.',DATE_SUB(NOW(),INTERVAL 4 DAY));

SET @demo_compra = LAST_INSERT_ID();

SET @asilosoft_usuario_id = @demo_admin;

UPDATE solicitacoes_compra SET situacao='Aprovada' WHERE id=@demo_compra;

SET @asilosoft_usuario_id = @demo_almoxarife;

INSERT INTO solicitacoes_compra(material_id,usuario_solicitante_id,quantidade,observacao,solicitada_em) VALUES (@demo_material_17,@demo_almoxarife,100,'Solicitação aguardando análise.',DATE_SUB(NOW(),INTERVAL 1 DAY));

INSERT INTO materiais(nome,tipo,descricao,unidade_medida,estoque_minimo) VALUES ('Detergente neutro','Material','Item fictício para demonstração.','litro',10);

SET @demo_material_18 = LAST_INSERT_ID();

INSERT INTO movimentacoes_estoque(material_id,tipo,quantidade,origem,usuario_id,observacao,movimentado_em) VALUES (@demo_material_18,'Entrada',150,'SUS',@demo_almoxarife,'Abastecimento de demonstração',DATE_SUB(NOW(),INTERVAL 30 DAY));

INSERT INTO movimentacoes_estoque(material_id,tipo,quantidade,origem,usuario_id,observacao,movimentado_em) VALUES (@demo_material_18,'Entrada',50,'SUS',@demo_almoxarife,'Abastecimento de demonstração',DATE_SUB(NOW(),INTERVAL 10 DAY));

INSERT INTO solicitacoes_compra(material_id,usuario_solicitante_id,quantidade,observacao,solicitada_em) VALUES (@demo_material_18,@demo_almoxarife,50,'Reposição recebida; entrada registrada separadamente.',DATE_SUB(NOW(),INTERVAL 10 DAY));

SET @demo_compra = LAST_INSERT_ID();

SET @asilosoft_usuario_id = @demo_admin;

UPDATE solicitacoes_compra SET situacao='Aprovada' WHERE id=@demo_compra;

UPDATE solicitacoes_compra SET situacao='Atendida' WHERE id=@demo_compra;

SET @asilosoft_usuario_id = @demo_almoxarife;

INSERT INTO solicitacoes_compra(material_id,usuario_solicitante_id,quantidade,observacao,solicitada_em) VALUES (@demo_material_18,@demo_almoxarife,50,'Recusada: quantidade revista após inventário.',DATE_SUB(NOW(),INTERVAL 8 DAY));

SET @demo_compra = LAST_INSERT_ID();

SET @asilosoft_usuario_id = @demo_admin;

UPDATE solicitacoes_compra SET situacao='Recusada' WHERE id=@demo_compra;

SET @asilosoft_usuario_id = @demo_almoxarife;

INSERT INTO solicitacoes_compra(material_id,usuario_solicitante_id,quantidade,observacao,solicitada_em) VALUES (@demo_material_18,@demo_almoxarife,50,'Reposição autorizada; aguardando fornecedor.',DATE_SUB(NOW(),INTERVAL 4 DAY));

SET @demo_compra = LAST_INSERT_ID();

SET @asilosoft_usuario_id = @demo_admin;

UPDATE solicitacoes_compra SET situacao='Aprovada' WHERE id=@demo_compra;

SET @asilosoft_usuario_id = @demo_almoxarife;

INSERT INTO solicitacoes_compra(material_id,usuario_solicitante_id,quantidade,observacao,solicitada_em) VALUES (@demo_material_18,@demo_almoxarife,50,'Solicitação aguardando análise.',DATE_SUB(NOW(),INTERVAL 1 DAY));

INSERT INTO materiais(nome,tipo,descricao,unidade_medida,estoque_minimo) VALUES ('Suplemento nutricional','Insumo','Item fictício para demonstração.','lata',25);

SET @demo_material_19 = LAST_INSERT_ID();

INSERT INTO movimentacoes_estoque(material_id,tipo,quantidade,origem,usuario_id,observacao,movimentado_em) VALUES (@demo_material_19,'Entrada',375,'Plano de Saúde',@demo_almoxarife,'Abastecimento de demonstração',DATE_SUB(NOW(),INTERVAL 30 DAY));

INSERT INTO movimentacoes_estoque(material_id,tipo,quantidade,origem,usuario_id,observacao,movimentado_em) VALUES (@demo_material_19,'Entrada',125,'Plano de Saúde',@demo_almoxarife,'Abastecimento de demonstração',DATE_SUB(NOW(),INTERVAL 10 DAY));

INSERT INTO solicitacoes_compra(material_id,usuario_solicitante_id,quantidade,observacao,solicitada_em) VALUES (@demo_material_19,@demo_almoxarife,125,'Reposição recebida; entrada registrada separadamente.',DATE_SUB(NOW(),INTERVAL 10 DAY));

SET @demo_compra = LAST_INSERT_ID();

SET @asilosoft_usuario_id = @demo_admin;

UPDATE solicitacoes_compra SET situacao='Aprovada' WHERE id=@demo_compra;

UPDATE solicitacoes_compra SET situacao='Atendida' WHERE id=@demo_compra;

SET @asilosoft_usuario_id = @demo_almoxarife;

INSERT INTO solicitacoes_compra(material_id,usuario_solicitante_id,quantidade,observacao,solicitada_em) VALUES (@demo_material_19,@demo_almoxarife,125,'Recusada: quantidade revista após inventário.',DATE_SUB(NOW(),INTERVAL 8 DAY));

SET @demo_compra = LAST_INSERT_ID();

SET @asilosoft_usuario_id = @demo_admin;

UPDATE solicitacoes_compra SET situacao='Recusada' WHERE id=@demo_compra;

SET @asilosoft_usuario_id = @demo_almoxarife;

INSERT INTO solicitacoes_compra(material_id,usuario_solicitante_id,quantidade,observacao,solicitada_em) VALUES (@demo_material_19,@demo_almoxarife,125,'Reposição autorizada; aguardando fornecedor.',DATE_SUB(NOW(),INTERVAL 4 DAY));

SET @demo_compra = LAST_INSERT_ID();

SET @asilosoft_usuario_id = @demo_admin;

UPDATE solicitacoes_compra SET situacao='Aprovada' WHERE id=@demo_compra;

SET @asilosoft_usuario_id = @demo_almoxarife;

INSERT INTO solicitacoes_compra(material_id,usuario_solicitante_id,quantidade,observacao,solicitada_em) VALUES (@demo_material_19,@demo_almoxarife,125,'Solicitação aguardando análise.',DATE_SUB(NOW(),INTERVAL 1 DAY));

INSERT INTO materiais(nome,tipo,descricao,unidade_medida,estoque_minimo) VALUES ('Avental descartável','Insumo','Item fictício para demonstração.','pct',10);

SET @demo_material_20 = LAST_INSERT_ID();

INSERT INTO movimentacoes_estoque(material_id,tipo,quantidade,origem,usuario_id,observacao,movimentado_em) VALUES (@demo_material_20,'Entrada',150,'Instituição',@demo_almoxarife,'Abastecimento de demonstração',DATE_SUB(NOW(),INTERVAL 30 DAY));

INSERT INTO movimentacoes_estoque(material_id,tipo,quantidade,origem,usuario_id,observacao,movimentado_em) VALUES (@demo_material_20,'Entrada',50,'Instituição',@demo_almoxarife,'Abastecimento de demonstração',DATE_SUB(NOW(),INTERVAL 10 DAY));

INSERT INTO solicitacoes_compra(material_id,usuario_solicitante_id,quantidade,observacao,solicitada_em) VALUES (@demo_material_20,@demo_almoxarife,50,'Reposição recebida; entrada registrada separadamente.',DATE_SUB(NOW(),INTERVAL 10 DAY));

SET @demo_compra = LAST_INSERT_ID();

SET @asilosoft_usuario_id = @demo_admin;

UPDATE solicitacoes_compra SET situacao='Aprovada' WHERE id=@demo_compra;

UPDATE solicitacoes_compra SET situacao='Atendida' WHERE id=@demo_compra;

SET @asilosoft_usuario_id = @demo_almoxarife;

INSERT INTO solicitacoes_compra(material_id,usuario_solicitante_id,quantidade,observacao,solicitada_em) VALUES (@demo_material_20,@demo_almoxarife,50,'Recusada: quantidade revista após inventário.',DATE_SUB(NOW(),INTERVAL 8 DAY));

SET @demo_compra = LAST_INSERT_ID();

SET @asilosoft_usuario_id = @demo_admin;

UPDATE solicitacoes_compra SET situacao='Recusada' WHERE id=@demo_compra;

SET @asilosoft_usuario_id = @demo_almoxarife;

INSERT INTO solicitacoes_compra(material_id,usuario_solicitante_id,quantidade,observacao,solicitada_em) VALUES (@demo_material_20,@demo_almoxarife,50,'Reposição autorizada; aguardando fornecedor.',DATE_SUB(NOW(),INTERVAL 4 DAY));

SET @demo_compra = LAST_INSERT_ID();

SET @asilosoft_usuario_id = @demo_admin;

UPDATE solicitacoes_compra SET situacao='Aprovada' WHERE id=@demo_compra;

SET @asilosoft_usuario_id = @demo_almoxarife;

INSERT INTO solicitacoes_compra(material_id,usuario_solicitante_id,quantidade,observacao,solicitada_em) VALUES (@demo_material_20,@demo_almoxarife,50,'Solicitação aguardando análise.',DATE_SUB(NOW(),INTERVAL 1 DAY));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_1,@demo_material_1,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 6 DAY),SHA2('demo-1-6',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_1,@demo_material_1,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 3 DAY),SHA2('demo-1-3',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_1,@demo_material_1,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 1 DAY),SHA2('demo-1-1',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_2,@demo_material_2,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 6 DAY),SHA2('demo-2-6',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_2,@demo_material_2,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 3 DAY),SHA2('demo-2-3',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_2,@demo_material_2,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 1 DAY),SHA2('demo-2-1',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_3,@demo_material_1,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 6 DAY),SHA2('demo-3-6',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_3,@demo_material_1,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 3 DAY),SHA2('demo-3-3',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_3,@demo_material_1,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 1 DAY),SHA2('demo-3-1',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_4,@demo_material_2,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 6 DAY),SHA2('demo-4-6',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_4,@demo_material_2,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 3 DAY),SHA2('demo-4-3',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_4,@demo_material_2,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 1 DAY),SHA2('demo-4-1',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_5,@demo_material_1,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 6 DAY),SHA2('demo-5-6',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_5,@demo_material_1,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 3 DAY),SHA2('demo-5-3',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_5,@demo_material_1,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 1 DAY),SHA2('demo-5-1',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_6,@demo_material_2,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 6 DAY),SHA2('demo-6-6',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_6,@demo_material_2,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 3 DAY),SHA2('demo-6-3',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_6,@demo_material_2,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 1 DAY),SHA2('demo-6-1',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_7,@demo_material_1,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 6 DAY),SHA2('demo-7-6',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_7,@demo_material_1,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 3 DAY),SHA2('demo-7-3',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_7,@demo_material_1,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 1 DAY),SHA2('demo-7-1',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_8,@demo_material_2,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 6 DAY),SHA2('demo-8-6',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_8,@demo_material_2,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 3 DAY),SHA2('demo-8-3',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_8,@demo_material_2,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 1 DAY),SHA2('demo-8-1',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_9,@demo_material_1,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 6 DAY),SHA2('demo-9-6',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_9,@demo_material_1,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 3 DAY),SHA2('demo-9-3',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_9,@demo_material_1,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 1 DAY),SHA2('demo-9-1',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_10,@demo_material_2,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 6 DAY),SHA2('demo-10-6',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_10,@demo_material_2,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 3 DAY),SHA2('demo-10-3',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_10,@demo_material_2,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 1 DAY),SHA2('demo-10-1',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_11,@demo_material_1,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 6 DAY),SHA2('demo-11-6',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_11,@demo_material_1,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 3 DAY),SHA2('demo-11-3',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_11,@demo_material_1,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 1 DAY),SHA2('demo-11-1',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_12,@demo_material_2,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 6 DAY),SHA2('demo-12-6',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_12,@demo_material_2,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 3 DAY),SHA2('demo-12-3',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_12,@demo_material_2,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 1 DAY),SHA2('demo-12-1',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_13,@demo_material_1,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 6 DAY),SHA2('demo-13-6',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_13,@demo_material_1,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 3 DAY),SHA2('demo-13-3',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_13,@demo_material_1,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 1 DAY),SHA2('demo-13-1',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_14,@demo_material_2,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 6 DAY),SHA2('demo-14-6',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_14,@demo_material_2,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 3 DAY),SHA2('demo-14-3',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_14,@demo_material_2,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 1 DAY),SHA2('demo-14-1',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_15,@demo_material_1,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 6 DAY),SHA2('demo-15-6',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_15,@demo_material_1,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 3 DAY),SHA2('demo-15-3',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_15,@demo_material_1,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 1 DAY),SHA2('demo-15-1',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_16,@demo_material_2,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 6 DAY),SHA2('demo-16-6',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_16,@demo_material_2,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 3 DAY),SHA2('demo-16-3',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_16,@demo_material_2,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 1 DAY),SHA2('demo-16-1',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_17,@demo_material_1,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 6 DAY),SHA2('demo-17-6',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_17,@demo_material_1,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 3 DAY),SHA2('demo-17-3',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_17,@demo_material_1,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 1 DAY),SHA2('demo-17-1',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_18,@demo_material_2,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 6 DAY),SHA2('demo-18-6',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_18,@demo_material_2,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 3 DAY),SHA2('demo-18-3',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_18,@demo_material_2,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 1 DAY),SHA2('demo-18-1',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_19,@demo_material_1,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 6 DAY),SHA2('demo-19-6',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_19,@demo_material_1,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 3 DAY),SHA2('demo-19-3',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_19,@demo_material_1,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 1 DAY),SHA2('demo-19-1',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_20,@demo_material_2,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 6 DAY),SHA2('demo-20-6',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_20,@demo_material_2,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 3 DAY),SHA2('demo-20-3',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_20,@demo_material_2,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 1 DAY),SHA2('demo-20-1',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_21,@demo_material_1,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 6 DAY),SHA2('demo-21-6',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_21,@demo_material_1,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 3 DAY),SHA2('demo-21-3',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_21,@demo_material_1,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 1 DAY),SHA2('demo-21-1',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_22,@demo_material_2,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 6 DAY),SHA2('demo-22-6',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_22,@demo_material_2,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 3 DAY),SHA2('demo-22-3',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_22,@demo_material_2,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 1 DAY),SHA2('demo-22-1',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_23,@demo_material_1,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 6 DAY),SHA2('demo-23-6',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_23,@demo_material_1,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 3 DAY),SHA2('demo-23-3',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_23,@demo_material_1,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 1 DAY),SHA2('demo-23-1',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_24,@demo_material_2,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 6 DAY),SHA2('demo-24-6',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_24,@demo_material_2,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 3 DAY),SHA2('demo-24-3',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_24,@demo_material_2,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 1 DAY),SHA2('demo-24-1',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_25,@demo_material_1,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 6 DAY),SHA2('demo-25-6',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_25,@demo_material_1,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 3 DAY),SHA2('demo-25-3',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_25,@demo_material_1,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 1 DAY),SHA2('demo-25-1',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_26,@demo_material_2,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 6 DAY),SHA2('demo-26-6',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_26,@demo_material_2,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 3 DAY),SHA2('demo-26-3',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_26,@demo_material_2,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 1 DAY),SHA2('demo-26-1',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_27,@demo_material_1,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 6 DAY),SHA2('demo-27-6',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_27,@demo_material_1,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 3 DAY),SHA2('demo-27-3',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_27,@demo_material_1,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 1 DAY),SHA2('demo-27-1',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_28,@demo_material_2,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 6 DAY),SHA2('demo-28-6',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_28,@demo_material_2,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 3 DAY),SHA2('demo-28-3',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_28,@demo_material_2,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 1 DAY),SHA2('demo-28-1',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_29,@demo_material_1,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 6 DAY),SHA2('demo-29-6',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_29,@demo_material_1,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 3 DAY),SHA2('demo-29-3',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_29,@demo_material_1,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 1 DAY),SHA2('demo-29-1',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_30,@demo_material_2,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 6 DAY),SHA2('demo-30-6',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_30,@demo_material_2,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 3 DAY),SHA2('demo-30-3',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_30,@demo_material_2,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 1 DAY),SHA2('demo-30-1',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_31,@demo_material_1,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 6 DAY),SHA2('demo-31-6',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_31,@demo_material_1,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 3 DAY),SHA2('demo-31-3',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_31,@demo_material_1,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 1 DAY),SHA2('demo-31-1',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_32,@demo_material_2,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 6 DAY),SHA2('demo-32-6',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_32,@demo_material_2,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 3 DAY),SHA2('demo-32-3',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_32,@demo_material_2,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 1 DAY),SHA2('demo-32-1',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_33,@demo_material_1,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 6 DAY),SHA2('demo-33-6',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_33,@demo_material_1,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 3 DAY),SHA2('demo-33-3',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_33,@demo_material_1,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 1 DAY),SHA2('demo-33-1',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_34,@demo_material_2,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 6 DAY),SHA2('demo-34-6',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_34,@demo_material_2,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 3 DAY),SHA2('demo-34-3',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_34,@demo_material_2,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 1 DAY),SHA2('demo-34-1',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_35,@demo_material_1,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 6 DAY),SHA2('demo-35-6',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_35,@demo_material_1,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 3 DAY),SHA2('demo-35-3',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_35,@demo_material_1,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 1 DAY),SHA2('demo-35-1',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_36,@demo_material_2,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 6 DAY),SHA2('demo-36-6',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_36,@demo_material_2,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 3 DAY),SHA2('demo-36-3',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_36,@demo_material_2,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 1 DAY),SHA2('demo-36-1',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_37,@demo_material_1,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 6 DAY),SHA2('demo-37-6',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_37,@demo_material_1,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 3 DAY),SHA2('demo-37-3',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_37,@demo_material_1,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 1 DAY),SHA2('demo-37-1',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_38,@demo_material_2,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 6 DAY),SHA2('demo-38-6',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_38,@demo_material_2,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 3 DAY),SHA2('demo-38-3',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_38,@demo_material_2,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 1 DAY),SHA2('demo-38-1',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_39,@demo_material_1,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 6 DAY),SHA2('demo-39-6',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_39,@demo_material_1,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 3 DAY),SHA2('demo-39-3',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_39,@demo_material_1,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 1 DAY),SHA2('demo-39-1',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_40,@demo_material_2,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 6 DAY),SHA2('demo-40-6',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_40,@demo_material_2,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 3 DAY),SHA2('demo-40-3',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_40,@demo_material_2,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 1 DAY),SHA2('demo-40-1',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_41,@demo_material_1,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 6 DAY),SHA2('demo-41-6',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_41,@demo_material_1,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 3 DAY),SHA2('demo-41-3',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_41,@demo_material_1,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 1 DAY),SHA2('demo-41-1',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_42,@demo_material_2,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 6 DAY),SHA2('demo-42-6',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_42,@demo_material_2,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 3 DAY),SHA2('demo-42-3',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_42,@demo_material_2,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 1 DAY),SHA2('demo-42-1',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_43,@demo_material_1,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 6 DAY),SHA2('demo-43-6',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_43,@demo_material_1,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 3 DAY),SHA2('demo-43-3',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_43,@demo_material_1,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 1 DAY),SHA2('demo-43-1',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_44,@demo_material_2,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 6 DAY),SHA2('demo-44-6',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_44,@demo_material_2,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 3 DAY),SHA2('demo-44-3',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_44,@demo_material_2,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 1 DAY),SHA2('demo-44-1',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_45,@demo_material_1,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 6 DAY),SHA2('demo-45-6',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_45,@demo_material_1,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 3 DAY),SHA2('demo-45-3',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_45,@demo_material_1,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 1 DAY),SHA2('demo-45-1',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_46,@demo_material_2,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 6 DAY),SHA2('demo-46-6',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_46,@demo_material_2,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 3 DAY),SHA2('demo-46-3',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_46,@demo_material_2,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 1 DAY),SHA2('demo-46-1',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_47,@demo_material_1,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 6 DAY),SHA2('demo-47-6',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_47,@demo_material_1,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 3 DAY),SHA2('demo-47-3',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_47,@demo_material_1,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 1 DAY),SHA2('demo-47-1',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_48,@demo_material_2,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 6 DAY),SHA2('demo-48-6',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_48,@demo_material_2,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 3 DAY),SHA2('demo-48-3',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_48,@demo_material_2,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 1 DAY),SHA2('demo-48-1',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_49,@demo_material_1,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 6 DAY),SHA2('demo-49-6',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_49,@demo_material_1,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 3 DAY),SHA2('demo-49-3',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_49,@demo_material_1,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 1 DAY),SHA2('demo-49-1',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_50,@demo_material_2,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 6 DAY),SHA2('demo-50-6',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_50,@demo_material_2,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 3 DAY),SHA2('demo-50-3',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_50,@demo_material_2,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 1 DAY),SHA2('demo-50-1',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_51,@demo_material_1,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 6 DAY),SHA2('demo-51-6',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_51,@demo_material_1,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 3 DAY),SHA2('demo-51-3',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_51,@demo_material_1,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 1 DAY),SHA2('demo-51-1',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_52,@demo_material_2,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 6 DAY),SHA2('demo-52-6',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_52,@demo_material_2,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 3 DAY),SHA2('demo-52-3',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_52,@demo_material_2,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 1 DAY),SHA2('demo-52-1',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_53,@demo_material_1,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 6 DAY),SHA2('demo-53-6',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_53,@demo_material_1,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 3 DAY),SHA2('demo-53-3',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_53,@demo_material_1,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 1 DAY),SHA2('demo-53-1',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_54,@demo_material_2,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 6 DAY),SHA2('demo-54-6',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_54,@demo_material_2,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 3 DAY),SHA2('demo-54-3',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_54,@demo_material_2,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 1 DAY),SHA2('demo-54-1',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_55,@demo_material_1,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 6 DAY),SHA2('demo-55-6',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_55,@demo_material_1,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 3 DAY),SHA2('demo-55-3',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_55,@demo_material_1,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 1 DAY),SHA2('demo-55-1',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_56,@demo_material_2,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 6 DAY),SHA2('demo-56-6',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_56,@demo_material_2,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 3 DAY),SHA2('demo-56-3',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_56,@demo_material_2,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 1 DAY),SHA2('demo-56-1',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_57,@demo_material_1,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 6 DAY),SHA2('demo-57-6',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_57,@demo_material_1,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 3 DAY),SHA2('demo-57-3',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_57,@demo_material_1,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 1 DAY),SHA2('demo-57-1',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_58,@demo_material_2,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 6 DAY),SHA2('demo-58-6',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_58,@demo_material_2,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 3 DAY),SHA2('demo-58-3',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_58,@demo_material_2,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 1 DAY),SHA2('demo-58-1',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_59,@demo_material_1,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 6 DAY),SHA2('demo-59-6',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_59,@demo_material_1,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 3 DAY),SHA2('demo-59-3',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_59,@demo_material_1,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 1 DAY),SHA2('demo-59-1',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_60,@demo_material_2,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 6 DAY),SHA2('demo-60-6',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_60,@demo_material_2,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 3 DAY),SHA2('demo-60-3',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_60,@demo_material_2,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 1 DAY),SHA2('demo-60-1',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_61,@demo_material_1,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 6 DAY),SHA2('demo-61-6',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_61,@demo_material_1,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 3 DAY),SHA2('demo-61-3',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_61,@demo_material_1,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 1 DAY),SHA2('demo-61-1',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_62,@demo_material_2,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 6 DAY),SHA2('demo-62-6',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_62,@demo_material_2,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 3 DAY),SHA2('demo-62-3',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_62,@demo_material_2,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 1 DAY),SHA2('demo-62-1',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_63,@demo_material_1,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 6 DAY),SHA2('demo-63-6',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_63,@demo_material_1,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 3 DAY),SHA2('demo-63-3',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_63,@demo_material_1,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 1 DAY),SHA2('demo-63-1',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_64,@demo_material_2,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 6 DAY),SHA2('demo-64-6',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_64,@demo_material_2,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 3 DAY),SHA2('demo-64-3',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_64,@demo_material_2,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 1 DAY),SHA2('demo-64-1',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_65,@demo_material_1,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 6 DAY),SHA2('demo-65-6',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_65,@demo_material_1,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 3 DAY),SHA2('demo-65-3',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_65,@demo_material_1,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 1 DAY),SHA2('demo-65-1',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_66,@demo_material_2,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 6 DAY),SHA2('demo-66-6',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_66,@demo_material_2,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 3 DAY),SHA2('demo-66-3',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_66,@demo_material_2,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 1 DAY),SHA2('demo-66-1',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_67,@demo_material_1,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 6 DAY),SHA2('demo-67-6',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_67,@demo_material_1,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 3 DAY),SHA2('demo-67-3',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_67,@demo_material_1,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 1 DAY),SHA2('demo-67-1',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_68,@demo_material_2,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 6 DAY),SHA2('demo-68-6',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_68,@demo_material_2,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 3 DAY),SHA2('demo-68-3',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_68,@demo_material_2,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 1 DAY),SHA2('demo-68-1',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_69,@demo_material_1,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 6 DAY),SHA2('demo-69-6',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_69,@demo_material_1,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 3 DAY),SHA2('demo-69-3',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_69,@demo_material_1,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 1 DAY),SHA2('demo-69-1',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_70,@demo_material_2,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 6 DAY),SHA2('demo-70-6',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_70,@demo_material_2,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 3 DAY),SHA2('demo-70-3',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_70,@demo_material_2,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 1 DAY),SHA2('demo-70-1',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_71,@demo_material_1,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 6 DAY),SHA2('demo-71-6',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_71,@demo_material_1,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 3 DAY),SHA2('demo-71-3',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_71,@demo_material_1,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 1 DAY),SHA2('demo-71-1',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_72,@demo_material_2,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 6 DAY),SHA2('demo-72-6',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_72,@demo_material_2,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 3 DAY),SHA2('demo-72-3',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_72,@demo_material_2,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 1 DAY),SHA2('demo-72-1',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_73,@demo_material_1,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 6 DAY),SHA2('demo-73-6',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_73,@demo_material_1,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 3 DAY),SHA2('demo-73-3',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_73,@demo_material_1,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 1 DAY),SHA2('demo-73-1',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_74,@demo_material_2,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 6 DAY),SHA2('demo-74-6',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_74,@demo_material_2,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 3 DAY),SHA2('demo-74-3',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_74,@demo_material_2,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 1 DAY),SHA2('demo-74-1',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_75,@demo_material_1,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 6 DAY),SHA2('demo-75-6',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_75,@demo_material_1,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 3 DAY),SHA2('demo-75-3',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_75,@demo_material_1,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 1 DAY),SHA2('demo-75-1',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_76,@demo_material_2,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 6 DAY),SHA2('demo-76-6',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_76,@demo_material_2,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 3 DAY),SHA2('demo-76-3',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_76,@demo_material_2,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 1 DAY),SHA2('demo-76-1',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_77,@demo_material_1,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 6 DAY),SHA2('demo-77-6',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_77,@demo_material_1,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 3 DAY),SHA2('demo-77-3',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_77,@demo_material_1,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 1 DAY),SHA2('demo-77-1',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_78,@demo_material_2,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 6 DAY),SHA2('demo-78-6',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_78,@demo_material_2,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 3 DAY),SHA2('demo-78-3',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_78,@demo_material_2,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 1 DAY),SHA2('demo-78-1',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_79,@demo_material_1,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 6 DAY),SHA2('demo-79-6',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_79,@demo_material_1,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 3 DAY),SHA2('demo-79-3',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_79,@demo_material_1,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 1 DAY),SHA2('demo-79-1',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_80,@demo_material_2,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 6 DAY),SHA2('demo-80-6',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_80,@demo_material_2,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 3 DAY),SHA2('demo-80-3',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_80,@demo_material_2,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 1 DAY),SHA2('demo-80-1',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_81,@demo_material_1,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 6 DAY),SHA2('demo-81-6',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_81,@demo_material_1,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 3 DAY),SHA2('demo-81-3',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_81,@demo_material_1,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 1 DAY),SHA2('demo-81-1',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_82,@demo_material_2,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 6 DAY),SHA2('demo-82-6',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_82,@demo_material_2,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 3 DAY),SHA2('demo-82-3',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_82,@demo_material_2,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 1 DAY),SHA2('demo-82-1',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_83,@demo_material_1,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 6 DAY),SHA2('demo-83-6',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_83,@demo_material_1,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 3 DAY),SHA2('demo-83-3',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_83,@demo_material_1,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 1 DAY),SHA2('demo-83-1',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_84,@demo_material_2,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 6 DAY),SHA2('demo-84-6',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_84,@demo_material_2,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 3 DAY),SHA2('demo-84-3',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_84,@demo_material_2,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 1 DAY),SHA2('demo-84-1',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_85,@demo_material_1,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 6 DAY),SHA2('demo-85-6',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_85,@demo_material_1,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 3 DAY),SHA2('demo-85-3',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_85,@demo_material_1,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 1 DAY),SHA2('demo-85-1',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_86,@demo_material_2,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 6 DAY),SHA2('demo-86-6',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_86,@demo_material_2,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 3 DAY),SHA2('demo-86-3',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_86,@demo_material_2,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 1 DAY),SHA2('demo-86-1',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_87,@demo_material_1,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 6 DAY),SHA2('demo-87-6',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_87,@demo_material_1,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 3 DAY),SHA2('demo-87-3',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_87,@demo_material_1,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 1 DAY),SHA2('demo-87-1',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_88,@demo_material_2,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 6 DAY),SHA2('demo-88-6',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_88,@demo_material_2,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 3 DAY),SHA2('demo-88-3',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_88,@demo_material_2,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 1 DAY),SHA2('demo-88-1',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_89,@demo_material_1,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 6 DAY),SHA2('demo-89-6',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_89,@demo_material_1,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 3 DAY),SHA2('demo-89-3',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_89,@demo_material_1,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 1 DAY),SHA2('demo-89-1',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_90,@demo_material_2,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 6 DAY),SHA2('demo-90-6',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_90,@demo_material_2,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 3 DAY),SHA2('demo-90-3',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_90,@demo_material_2,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 1 DAY),SHA2('demo-90-1',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_91,@demo_material_1,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 6 DAY),SHA2('demo-91-6',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_91,@demo_material_1,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 3 DAY),SHA2('demo-91-3',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_91,@demo_material_1,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 1 DAY),SHA2('demo-91-1',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_92,@demo_material_2,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 6 DAY),SHA2('demo-92-6',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_92,@demo_material_2,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 3 DAY),SHA2('demo-92-3',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_92,@demo_material_2,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 1 DAY),SHA2('demo-92-1',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_93,@demo_material_1,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 6 DAY),SHA2('demo-93-6',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_93,@demo_material_1,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 3 DAY),SHA2('demo-93-3',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_93,@demo_material_1,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 1 DAY),SHA2('demo-93-1',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_94,@demo_material_2,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 6 DAY),SHA2('demo-94-6',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_94,@demo_material_2,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 3 DAY),SHA2('demo-94-3',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_94,@demo_material_2,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 1 DAY),SHA2('demo-94-1',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_95,@demo_material_1,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 6 DAY),SHA2('demo-95-6',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_95,@demo_material_1,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 3 DAY),SHA2('demo-95-3',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_95,@demo_material_1,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 1 DAY),SHA2('demo-95-1',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_96,@demo_material_2,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 6 DAY),SHA2('demo-96-6',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_96,@demo_material_2,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 3 DAY),SHA2('demo-96-3',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_96,@demo_material_2,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 1 DAY),SHA2('demo-96-1',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_97,@demo_material_1,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 6 DAY),SHA2('demo-97-6',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_97,@demo_material_1,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 3 DAY),SHA2('demo-97-3',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_97,@demo_material_1,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 1 DAY),SHA2('demo-97-1',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_98,@demo_material_2,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 6 DAY),SHA2('demo-98-6',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_98,@demo_material_2,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 3 DAY),SHA2('demo-98-3',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_98,@demo_material_2,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 1 DAY),SHA2('demo-98-1',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_99,@demo_material_1,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 6 DAY),SHA2('demo-99-6',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_99,@demo_material_1,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 3 DAY),SHA2('demo-99-3',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_99,@demo_material_1,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 1 DAY),SHA2('demo-99-1',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_100,@demo_material_2,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 6 DAY),SHA2('demo-100-6',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_100,@demo_material_2,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 3 DAY),SHA2('demo-100-3',256));

INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao,usado_em,chave_requisicao) VALUES (@demo_idoso_100,@demo_material_2,@demo_saude,2,'Cuidados de higiene — demonstração',DATE_SUB(NOW(),INTERVAL 1 DAY),SHA2('demo-100-1',256));

INSERT INTO movimentacoes_estoque(material_id,tipo,quantidade,usuario_id,observacao) VALUES (@demo_material_10,'Saída',39.0,@demo_almoxarife,'Distribuição interna para uso nas alas — demonstração');

INSERT INTO movimentacoes_estoque(material_id,tipo,quantidade,usuario_id,observacao) VALUES (@demo_material_11,'Saída',97.5,@demo_almoxarife,'Distribuição interna para uso nas alas — demonstração');

INSERT INTO movimentacoes_estoque(material_id,tipo,quantidade,usuario_id,observacao) VALUES (@demo_material_18,'Saída',195.0,@demo_almoxarife,'Distribuição interna para uso nas alas — demonstração');
