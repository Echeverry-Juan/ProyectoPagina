import React, { useState } from "react";
import './Estilos.css';

const JuegosFilterForm = ({ onSubmit, loading }) => {
  const [plataforma, setPlataforma] = useState("");
  const [texto, setTexto] = useState("");
  const [clasificacion, setClasificacion] = useState("");

  const handleSubmit = (e) => {
    e.preventDefault();


    const filtros = {};
    if (plataforma) filtros.plataforma = plataforma;
    if (texto) filtros.texto = texto;
    if (clasificacion) filtros.clasificacion = clasificacion;

    onSubmit(filtros);
  };

  return (
    <form className="formulario-contenedor-filtro" onSubmit={handleSubmit}>
      <div className="formulario-contenedor-filtro-texto">
        <label>Texto</label>
        <input
          type="text"
          value={texto}
          onChange={(e) => setTexto(e.target.value)}
        />
      </div>
      <div>
        <label>Clasificación</label>
        <select
          value={clasificacion}
          onChange={(e) => setClasificacion(e.target.value)}
        >
          <option value="">--Selecciona una clasificación--</option>
          <option value="ATP">ATP</option>
          <option value="+13">+13</option>
          <option value="+18">+18</option>
        </select>
      </div>
      <div>
        <label>Plataforma</label>
        <select
          value={plataforma}
          onChange={(e) => setPlataforma(e.target.value)}
        >
          <option value="">--Selecciona una plataforma--</option>
          <option value="PS">PS</option>
          <option value="XBOX">XBOX</option>
          <option value="PC">PC</option>
          <option value="Android">Android</option>
          <option value="Otro">Otro</option>
        </select>
      </div>
      <button type="submit" disabled={loading}>
        Filtrar
      </button>
    </form>
  );
};

export default JuegosFilterForm;