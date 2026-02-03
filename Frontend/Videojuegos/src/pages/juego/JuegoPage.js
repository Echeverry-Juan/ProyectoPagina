
import React, { useEffect, useState } from "react";
import axios from "axios";
import JuegoCard from "../../components/JuegoCard";
import JuegosFilterForm from "../../components/JuegoFilterForm";
import "../../components/Estilos.css";
import Header from "../../components/HeaderComponent";
import NavBar from "../../components/NavBarComponent";
import CalificacionEstrella from "../../components/CalificacionEstrellas";

const url = process.env.REACT_APP_BACK_URL;

const Juegos = () => {
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState("");
  const [juegos, setJuegos] = useState([]);
  const [filtros, setFiltros] = useState({}); 
  const [pagina, setPagina] = useState(1); 
  const [totalPaginas, setTotalPaginas] = useState(1); 
  const [calificacion, setCalificacion] = useState(false);
  
  useEffect(() => {
    loadData(); 
  }, [filtros, pagina,calificacion]); 


  const loadData = () => {
    setLoading(true);
    setCalificacion(false);
    setError("");
    setJuegos([]);
  


    const params = new URLSearchParams({ ...filtros, pagina });

    axios
      .get(`${url}/juegos?${params.toString()}`)
      .then((response) => {
        setError("");
        setLoading(false);
        setJuegos(response.data.juegos); 
        setTotalPaginas(response.data.totalPaginas);
      })
      .catch((error) => {
        setLoading(false);
        setError(error.response?.data || "Error al cargar los datos");
      });
  };


  const handleFilterSubmit = (nuevosFiltros) => {
    setFiltros(nuevosFiltros); 
    setPagina(1); 
  };


  const handlePageChange = (nuevaPagina) => {
    setJuegos([]); 
    setPagina(nuevaPagina);
  };
  


  const resetFiltrosYPagina = () => {
    setFiltros({}); 
    setPagina(1); 
  };

  const nuevoProm=()=>{
    setCalificacion(true);
  }


  const juegosCards = juegos.map((juego) => (
    <li key={juego.id}>
      <JuegoCard juego={juego}></JuegoCard>
      <CalificacionEstrella juego={juego} onRatingChange={nuevoProm}/>
    </li>
  ));

  return (
    <section>
      <header>
        <Header />
      </header>
      <NavBar onResetFiltros={resetFiltrosYPagina} />
      <h2 className="tituloPage">Juegos</h2>
      <div className="juego-page">
      <JuegosFilterForm onSubmit={handleFilterSubmit} loading={loading} />
      {error ? (
        <h2>{error}</h2>
      ) : juegosCards.length ? (
        <ul className="card-container">{juegosCards}</ul>
      ) : (
        loading && <h2>Cargando...</h2>
      )}
      <div className="paginacion">
        <button
          onClick={() => handlePageChange(pagina - 1)}
          disabled={pagina === 1}
        >
          Anterior
        </button>
        <span>Página {pagina} de {totalPaginas}</span>
        <button
          onClick={() => handlePageChange(pagina + 1)}
          disabled={pagina === totalPaginas}
        >
          Siguiente
        </button>
      </div>
      </div>
    </section>
  );
};

export default Juegos;


