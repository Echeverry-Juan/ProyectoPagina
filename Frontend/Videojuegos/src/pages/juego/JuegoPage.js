import React, { useEffect, useState, useRef, useCallback } from "react";
import axios from "axios";
import JuegoCard from "../../components/JuegoCard";
import '../../components/Estilos.css';
import Header from "../../components/HeaderComponent";
import NavBar from "../../components/NavBarComponent";


const url = process.env.REACT_APP_BACK_URL;

const Juegos = () => {
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState("");
    const [juegos, setJuegos] = useState([]);
  
    useEffect(()=>{
    loadData()
  },[]
  )
  const loadData= ()=>{
    axios
    .get(`${url}/juegos`)
    .then((response) => {
      setError(false);
      setLoading(false);
      setJuegos(response.data);
      
    })
    .catch((error) => {
      setLoading(false);
      setError(error);
      
    });
  }

  const juegosCards= juegos.map((juego)=>{
    return(
      <li key={juego.id}>
        <JuegoCard juego={juego} ></JuegoCard> 
      </li>
    )
  })

  return(
    <section>
      <header>
      <Header/>
      </header>
      <NavBar/>
      <h2 className="tituloPage">Juegos</h2>
      {error?(
        <h2>Algo ha salido mal</h2>
      ):juegosCards.length?(
            <ul className="card-container">{juegosCards}</ul>
      ): loading&&(<h2>Cargando...</h2>)
          
        }
      

    </section>
  )
}


export default Juegos;