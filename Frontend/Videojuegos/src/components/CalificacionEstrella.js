import React, { useEffect, useState } from "react";
import axios from "axios";
import { FaStar } from "react-icons/fa";

const url = process.env.REACT_APP_BACK_URL;

const CalificacionEstrella = ({ juego, onRatingChange }) => {
  const [rating, setRating] = useState(0);
  const [hover, setHover] = useState(null);
  const [idCalificacion, setIdCalificacion] = useState(null);
  const [tieneCalificacion, setTieneCalificacion] = useState(false);

  const token = localStorage.getItem("token");

  // 🔹 1. GET → saber si el usuario ya calificó este juego
  useEffect(() => {
    if (!token) return;

    axios
      .get(`${url}/calificacion`, {
        params: {
          idjuego: juego.id,
          token,
        },
      })
      .then((res) => {
        if (res.data.calificaciones?.length > 0) {
          const calif = res.data.calificaciones[0];
          setRating(calif.estrellas);
          setIdCalificacion(calif.id); // ✅ ID DE LA CALIFICACIÓN
          setTieneCalificacion(true);
        }
      })
      .catch(() => {
        // silencio intencional
      });
  }, [juego.id, token]);

  // 🔹 2. POST o PUT según exista o no
  const handleClick = (valor) => {
    setRating(valor);

    if (!token) return;

    // 👉 POST (crear)
    if (!tieneCalificacion) {
      axios
        .post(`${url}/calificacion`, {
          estrellas: valor,
          idjuego: juego.id,
          token,
        })
        .then((res) => {
          setIdCalificacion(res.data.id); // 🔑 CLAVE
          setTieneCalificacion(true);
          onRatingChange?.();
        });
    }

    // 👉 PUT (actualizar)
    else {
      axios
        .put(`${url}/calificacion/${idCalificacion}`, {
          estrellas: valor,
          token,
        })
        .then(() => {
          onRatingChange?.();
        });
    }
  };

  // 🔹 3. DELETE
  const eliminarCalificacion = () => {
    axios
      .delete(`${url}/calificacion/${idCalificacion}`, {
        data: { token },
      })
      .then(() => {
        setRating(0);
        setIdCalificacion(null);
        setTieneCalificacion(false);
        onRatingChange?.();
      });
  };

  return (
    <div className="calificacion-container">
      <p>
        {tieneCalificacion
          ? `Tu calificación: ${rating} / 5`
          : "Calificá este juego:"}
      </p>

      <div className="estrellas">
        {[...Array(5)].map((_, i) => {
          const valor = i + 1;
          return (
            <FaStar
              key={valor}
              size={24}
              className="estrella"
              color={valor <= (hover || rating) ? "#ffc107" : "#e4e5e9"}
              onClick={() => handleClick(valor)}
              onMouseEnter={() => setHover(valor)}
              onMouseLeave={() => setHover(null)}
              style={{ cursor: "pointer" }}
            />
          );
        })}
      </div>

      {tieneCalificacion && (
        <button className="btn-eliminar" onClick={eliminarCalificacion}>
          ELIMINAR CALIFICACIÓN
        </button>
      )}
    </div>
  );
};

export default CalificacionEstrella;
