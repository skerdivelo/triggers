CREATE DATABASE IF NOT EXISTS Libreria;
USE Libreria;

CREATE TABLE Libri (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titolo VARCHAR(100) NOT NULL,
    autore VARCHAR(100) NOT NULL,
    prezzo DECIMAL(8,2) NOT NULL,
    quantita INT NOT NULL
);

CREATE TABLE Acquisti (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_libro INT,
    data_acquisto DATE,
    quantita_acquistata INT,
    FOREIGN KEY (id_libro) REFERENCES Libri(id)
);

DELIMITER //
CREATE TRIGGER decrementa_quantita
AFTER INSERT ON Acquisti -- trigger scatenato dopo l'inserimento di una riga in Acquisti
FOR EACH ROW -- per ogni riga inserita
BEGIN
    DECLARE new_quantita INT; -- dichiarazione di una variabile
    SET new_quantita = (SELECT quantita FROM Libri WHERE id = NEW.id_libro) - NEW.quantita_acquistata; -- calcolo della nuova quantita
    IF new_quantita >= 0 THEN -- se la nuova quantita e' maggiore o uguale a 0
        UPDATE Libri -- aggiornamento della quantita
        SET quantita = new_quantita
        WHERE id = NEW.id_libro;
    ELSE
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Quantita non può essere inferiore 0'; -- altrimenti segnala un errore
    END IF;
END;
//
DELIMITER ;

-- aumenta_quantita
DELIMITER //
CREATE TRIGGER aumenta_quantita
AFTER DELETE ON Acquisti -- trigger scatenato dopo l'eliminazione di una riga in Acquisti
FOR EACH ROW -- per ogni riga eliminata
BEGIN
    DECLARE new_quantita INT; -- dichiarazione di una variabile
    SET new_quantita = (SELECT quantita FROM Libri WHERE id = OLD.id_libro) + OLD.quantita_acquistata; -- calcolo della nuova quantita
    UPDATE Libri -- aggiornamento della quantita
    SET quantita = new_quantita
    WHERE id = OLD.id_libro;
END;
//
DELIMITER ;

-- inserimento dati
INSERT INTO Libri (titolo, autore, prezzo, quantita) VALUES
('Il signore degli anelli', 'J.R.R. Tolkien', 25.00, 10),
('Il nome della rosa', 'Umberto Eco', 20.00, 5),
('Il codice da Vinci', 'Dan Brown', 15.00, 8),
('La solitudine dei numeri primi', 'Paolo Giordano', 18.00, 3),
('Il vecchio e il mare', 'Ernest Hemingway', 12.00, 6);

INSERT INTO Acquisti (id_libro, data_acquisto, quantita_acquistata) VALUES
(1, '2021-01-01', 2),
(2, '2021-01-02', 1),
(3, '2021-01-03', 3),
(4, '2021-01-04', 1),
(5, '2021-01-05', 2);
```