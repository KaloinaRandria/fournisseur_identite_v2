
truncate table tentative_mdp_failed cascade;
alter sequence tentative_mdp_failed_id_seq restart with 1 ;

truncate table tentative_pin_failed cascade;
alter sequence tentative_pin_failed_id_seq restart with 1 ;

truncate table jeton_authentification cascade;
alter sequence jeton_authentification_id_seq restart with 1 ;

truncate table jeton_inscription cascade;
alter sequence jeton_inscription_id_seq restart with 1 ;

truncate table jeton cascade;
alter sequence jeton_id_seq restart with 1 ;

truncate table utilisateur cascade;
alter sequence utilisateur_id_seq restart with 1 ;
