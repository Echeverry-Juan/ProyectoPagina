import axios from "axios";
import { useEffect, useState } from "react";
import { useParams } from "react-router-dom";
import JuegoCard from "../../components/JuegoCard";
import Header from "../../components/HeaderComponent";
import NavBar from "../../components/NavBarComponent";
import CalificacionCard from "../../components/CalificacionCard";
import  '../../components/CalificacionCard.css';
import CalificacionEstrella from "../../components/CalificacionEstrellas";

const url = process.env.REACT_APP_BACK_URL;

const Juego = () => {
  const { id } = useParams();
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(false);
  const [juego, setJuego] = useState(null);
  const [calificaciones, setCalificaciones] = useState([]);
  const [calificacion, setCalificacion] = useState([]);

  useEffect(() => {
    loadData();
  }, [calificacion]);

  const loadData = () => {
    setLoading(true);
    setCalificacion(false);
    setJuego([]);
    setCalificaciones([]);

    axios
      .get(`${url}/juegos/${id}`)
      .then((response) => {
        setError(false);
        setLoading(false);
        setJuego(response.data.juego);
        setCalificaciones(response.data.calificaciones);
      })
      .catch((error) => {
        setLoading(false);
        setError(error.response.data);
      });
  };


  const nuevoProm=()=>{
    setCalificacion(true);
  }


  const renderJuego = () => {
    return juego ? (<div>
        <JuegoCard juego={juego} />
        <CalificacionEstrella juego={juego} onRatingChange={nuevoProm}/>
        </div>
    ) : (
      <li className="card">No existe información del juego</li>
    );
  };

  const renderCalificaciones = () => {
    return calificaciones.length > 0 ? (
      calificaciones.map((calificacion) => (
        <li key={calificacion.idcalificacion}>
          <CalificacionCard calificacion={calificacion} />
        </li>
      ))
    ) : (
      <li className="calificacion-card"><p className="calificacion-card-title">No posee calificaciones</p></li>
    );
  };

  return (
    <div>
      <Header />
      <NavBar />
      <h2 className="tituloPage">Juego y Calificaciones</h2>
      <div className="cards">
        {error ? (
          <h2>Algo ha salido mal</h2>
        ) : loading ? (
          <h2>Cargando...</h2>
        ) : (
          <>
            <ul className="card-container">{renderJuego()}</ul>
            <ul className="card-container">{renderCalificaciones()}</ul>
          </>
        )}
      </div>
    </div>
  );
};

export default Juego;
