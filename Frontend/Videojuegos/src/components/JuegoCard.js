import React from "react";
import { Link } from "react-router-dom";
import './JuegoCard.css';

function JuegoCard(props){
    const {juego}=props
    const imagenSrc = `data:image/jpg;base64,${juego.imagen}`;
    return (
        <div className="juego-card">
          <img src={imagenSrc} alt={juego.nombre} />
          <div className="juego-card-content">
          <h3 className="juego-card-title"><Link to={`/juegos/${juego.id}`}>{juego.nombre}</Link></h3>
            <p className="juego-card-description">{juego.descripcion}</p>
            
            <div className="juego-card-info">
              <span className="juego-card-age">{juego.clasificacion_edad}</span>
              <span className="juego-card-platform">{juego.plataforma}</span>
            </div>
    
            <div className="juego-card-footer">
              <span className="juego-card-calificacion">
                Calificación: <span>{juego.puntuacion_promedio}</span> / 5
              </span>
            </div>
          </div>
        </div>
      );
    }

export default JuegoCard;