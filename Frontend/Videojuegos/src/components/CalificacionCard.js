import React from "react";
import Estrellas from "./Estrellas";
import './CalificacionCard.css';

function CalificacionCard(props){
    //const {nombre}=nombre
    const {calificacion}=props
    function cantestrellas(calificacion){
        var estrella=[];
        for(var i=0 ; i<calificacion.estrellas; i++){
            estrella[i]=i ; 
        }
          
        const estrellas=estrella.map((estrella)=>{
            return(
              <li key={estrella}>
                <Estrellas/> 
              </li>
            )
          })
          return estrellas;
        } 
        
    

    const cantidadEstrellas=cantestrellas(calificacion);

    return(
        <div className="calificacion-card">
        <article className="calificacion-card-content">
            <h3>Calificacion</h3>
            <p>Usuario {calificacion.usuario}</p>
            <p className="calificacion-card-info">Calificacion <ul className="calificacion-card-info ">{cantidadEstrellas}</ul></p>
            
        </article>
        </div>
    )
}

export default CalificacionCard;