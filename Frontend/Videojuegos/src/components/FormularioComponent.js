import React, { useState } from "react";
import './Estilos.css';
import { verificarClave, verificarNombre } from "./VerificarDatos";
import { Link } from "react-router-dom";

const Formulario = ({ onData, loading }) => {
  const [nombre_usuario, setNombre_Usuario] = useState('');
  const [clave, setClave] = useState('');
  const [error, setError] = useState('');


  const handleSubmit = (e) => {
    e.preventDefault();
    setError('');

    if (!verificarNombre(nombre_usuario)) {
      setError('El nombre de usuario debe ser alfanumérico y tener entre 6 y 20 caracteres.');
      return;
    }

    if (!verificarClave(clave)) {
      setError('La clave debe tener al menos 8 caracteres, incluir mayúsculas, minúsculas, números y caracteres especiales.');
      return;
    }

    onData({ nombre_usuario, clave });
  };

  return (
    <form className="formulario-contenedor" onSubmit={handleSubmit}>
      <div>
        <input
          type="text"
          placeholder="Nombre de Usuario"
          value={nombre_usuario}
          onChange={(e) => setNombre_Usuario(e.target.value)}
          required
        />
      </div>
      <div>

        <input
          type="password"
          placeholder="clave"
          value={clave}
          onChange={(e) => setClave(e.target.value)}
          required
        />
      </div>
      <button type="submit" disabled={loading}>
        {loading ? 'Enviando' : 'Enviar'}
      </button>
      {error && <p>{error}</p>}
    </form>
    
  );
};

export default Formulario;
