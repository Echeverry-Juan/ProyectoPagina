import { useState, useEffect } from 'react';
import { FaStar } from 'react-icons/fa';
import axios from 'axios';
import './JuegoCard.css';
import { Link } from 'react-router-dom';

export default function CalificacionEstrella({ juego , onRatingChange}) {
    const token = localStorage.getItem("token");
    const tiempotoken = localStorage.getItem("token-time");
    const tiempoact = new Date().getTime();

    const [userRating, setUserRating] = useState(null);
    const [tieneCalificacion, setTieneCalificacion] = useState(false);
    const [hoverRating, setHoverRating] = useState(null);
    const [id, setId] = useState(null);

    const url = process.env.REACT_APP_BACK_URL;
    const nombre_usuario = localStorage.getItem('nombre_usuario');

    useEffect(() => {
        if (!juego || !juego.nombre) return;

        axios.get(`${url}/calificacion`, {
            params: {
                nombre_usuario: nombre_usuario,
                nombre_juego: juego.nombre
            }
        })
        .then(response => {
            const calificaciones = response.data.calificaciones;
            if (calificaciones && calificaciones.length > 0) {
                setTieneCalificacion(true);
                setUserRating(calificaciones[0].estrellas);
                setId(calificaciones[0].id);
            } else {
                setTieneCalificacion(false);
            }
        })
        .catch(error => {
            console.error("Error al obtener la calificación del juego:", error);
        });
    }, [juego,nombre_usuario, url]);

    const handleRating = (rating) => {
        setUserRating(rating);
    
        if (tieneCalificacion) {
          axios.put(`${url}/calificacion/${id}`, {
            estrellas: rating,
            token: token,
          })
          .then(() => {
            if (onRatingChange) onRatingChange();
          })
          .catch(error => console.error('Error en actualizar calificación:', error));
        } else {
          axios.post(`${url}/calificacion`, {
            estrellas: rating,
            idjuego: juego.id,
            token,
          })
          .then((response) => {
            setId(response.data);
            setTieneCalificacion(true);
            if (onRatingChange) onRatingChange();
          })
          .catch(error => console.error('Error en crear calificación:', error));
        }
      };
    
      const handleDelete = () => {
        axios.delete(`${url}/calificacion/${id}`, {
          data: { token, juego_id: juego.id },
        })
        .then(() => {
          setUserRating(null);
          setTieneCalificacion(false);
          if (onRatingChange) onRatingChange();
        })
        .catch(error => console.error('Error al eliminar calificación:', error));
      };
    
    
//cambios
    if (!token || !tiempotoken || tiempotoken < tiempoact) {
        localStorage.removeItem("token");
        localStorage.removeItem("token-time");
        return (
            <div>
                <span className='juego-card-content'>
                    Para calificar o ver sus calificaciones
                    <Link to="/login"> inicie sesión</Link>
                </span>
            </div>
        );
    }
    

    if (!juego || !juego.id) {
        return <p>Datos del juego no disponibles.</p>;
    }

    return (
        <div className='juego-card-estrellas'>
            <div >
                {userRating ? (
                    <p>Tu calificación: {userRating} / 5</p>
                ) : (
                    <p>Califica este juego:</p>
                )}
                <div>
                    {[...Array(5)].map((_, index) => {
                        const ratingValue = index + 1;
                        return (
                            <label key={index}>
                                <input
                                    type="radio"
                                    name={`rating-${juego.id}`}
                                    value={ratingValue}
                                    onClick={() => handleRating(ratingValue)}
                                    style={{ display: "none" }}
                                />
                                <FaStar
                                    className="star"
                                    size={20}
                                    color={ratingValue <= (hoverRating || userRating) ? "#ffc107" : "#e4e5e9"}
                                    onMouseEnter={() => setHoverRating(ratingValue)}
                                    onMouseLeave={() => setHoverRating(null)}
                                />
                            </label>
                        );
                    })}
                </div>
            </div>
            {userRating !== null && (
                <button onClick={handleDelete}>Eliminar calificación</button>
            )}
        </div>
    );
    
}
