import React from "react";
import './CalificacionCard.css';
import { FaStar } from "react-icons/fa";

function CalificacionCard(props){
    const {calificacion}=props
    function cantestrellas(calificacion){
        var estrella=[];
        for(var i=0 ; i<calificacion.estrellas; i++){
            estrella[i]=i ; 
        }
          
        const estrellas=estrella.map((estrella)=>{
            return(
              <li key={estrella}>
                <FaStar/> 
              </li>
            )
          })
          return estrellas;
        } 
    const usuarioact= localStorage.getItem("nombre_usuario");
    

    const cantidadEstrellas=cantestrellas(calificacion);
    if(calificacion.usuario === usuarioact){
      return(
        <div className="calificacion-card-actual">
        <article className="calificacion-card-actual-content">
            <h3 className="calificacion-card-actual-title">Tu Calificacion</h3>
            <p className="calificacion-card-actual-usuario">Nombre Usuario: {calificacion.usuario}</p>
            <p className="calificacion-card-actual-calificacion">Calificacion <ul className="calificacion-card-actual-info ">{cantidadEstrellas}</ul></p>
            
        </article>
        </div>
      )
    }else{
    return(
        <div className="calificacion-card">
        <article className="calificacion-card-content">
            <h3 className="calificacion-card-title">Calificacion</h3>
            <p className="calificacion-card-usuario">Nombre Usuario: {calificacion.usuario}</p>
            <p className="calificacion-card-calificacion">Calificacion <ul className="calificacion-card-info ">{cantidadEstrellas}</ul></p>
            
        </article>
        </div>
    )
  }
}

export default CalificacionCard;