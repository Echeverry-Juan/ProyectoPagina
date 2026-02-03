import React, { useState } from "react";
import './Estilos.css';


const JuegoForm = ({ onSubmit, loading, setDatosjuego, datosjuego }) => {

  const [imagenCargada, setImagenCargada] = useState(false);

  const opcionesClasificacion = ["","ATP", "+13", "+18"];
  const opcionesPlataformas = ["PS", "Xbox", "PC", "Android", "Otro"];

  const handlePlataformaChange = (e) => {
    const { value, checked } = e.target;
    setDatosjuego((prev) => {
      const plataformasActualizadas = checked
        ? [...prev.plataformas, value]
        : prev.plataformas.filter((p) => p !== value);

      return { ...prev, plataformas: plataformasActualizadas };
    });
  };

  const handleImagenChange = (e) => {
    const file = e.target.files[0];
    if (file && file.type === "image/jpeg") {
      const reader = new FileReader();
      reader.onloadend = () => {
        setDatosjuego({ ...datosjuego, imagen: reader.result });
        setImagenCargada(true);
      };
      reader.readAsDataURL(file);
    } else {
      alert("Por favor, sube una imagen en formato JPEG");
      e.target.value = "";
      setImagenCargada(false);
    }
  };

  const handleSubmit = (e) => {
    e.preventDefault();

    if (datosjuego.nombre.length > 45) {
      alert("El nombre no debe superar los 45 caracteres.");
      return;
    }

    if (!imagenCargada) {
      alert("Por favor, espera a que la imagen se cargue.");
      return;
    }

    onSubmit(datosjuego);
  };

  return (
    <form className="formulario-contenedor" onSubmit={handleSubmit}>
      <div>
        <label>Nombre (máximo 45 caracteres):</label>
        <input
          type="text"
          value={datosjuego.nombre}
          onChange={(e) => setDatosjuego({ ...datosjuego, nombre: e.target.value })}
          maxLength={45}
          required
        />
      </div>

      <div>
        <label>Descripción:</label>
        <textarea
          value={datosjuego.descripcion}
          onChange={(e) => setDatosjuego({ ...datosjuego, descripcion: e.target.value })}
          required
        />
      </div>

      <div>
        <label>Imagen (JPEG):</label>
        <input
          type="file"
          accept="image/jpeg"
          onChange={handleImagenChange}
          required
        />
      </div>

      <div>
        <label>Clasificación por edad:</label>
        <select
          value={datosjuego.clasificacion }
          onChange={(e) => setDatosjuego((prev) => ({ ...prev, clasificacion: e.target.value }))}
          required
        >
          {opcionesClasificacion.map((opcion) => (
            <option key={opcion} value={opcion}>
              {opcion}
            </option>
          ))}
        </select>
      </div>

      <div>
        <label>Plataformas:</label>
        {opcionesPlataformas.map((plataforma) => (
          <div key={plataforma}>
            <input
              type="checkbox"
              value={plataforma}
              checked={datosjuego.plataformas.includes(plataforma)}
              onChange={handlePlataformaChange}
            />
            <label>{plataforma}</label>
          </div>
        ))}
      </div>

      <button type="submit" disabled={loading}>
        Crear Juego
      </button>
    </form>
  );
};

export default JuegoForm;
