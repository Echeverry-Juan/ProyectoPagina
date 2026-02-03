import React, { useState } from "react";
import axios from "axios";
import JuegoForm from "../../components/JuegoForm";
import Header from "../../components/HeaderComponent";
import NavBar from "../../components/NavBarComponent";
import '../../components/Estilos.css';

const url = process.env.REACT_APP_BACK_URL;

const Admin = () => {
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState("");
  const [success, setSuccess] = useState("");
  const [datosjuego, setDatosjuego] = useState({
    nombre: '',
    descripcion: '',
    clasificacion: '',
    plataformas: [],
    imagen: ''
  }); 


  const Crearjuego = () => {
    setLoading(true);
    setError("");
  
    const token = localStorage.getItem("token");
  
    if (!token) {
      setError("Token no disponible.");
      setLoading(false);
      return;
    }
  
    const params = {
      token,
      nombre: datosjuego.nombre,
      descripcion: datosjuego.descripcion,
      clasificacion: datosjuego.clasificacion,
      imagen: datosjuego.imagen,
      plataformas: datosjuego.plataformas 
    };
    
    console.log(params);

    axios
      .post(`${url}/juego`, params)
      .then((response) => {
        setError("");
        setLoading(false);
        setSuccess(response.data);
      })
      .catch((error) => {
        console.error("Error en la creación del juego:", error);
        setLoading(false);
        setError(error.response.data|| "Error al crear el juego");
      });      
  };
  

  

  const handleCrear = () => {
    Crearjuego();
  };



  return (
    <section>
      <header>
        <Header />
      </header>
      <NavBar />
      
      <div className="formularioPage">
      
        <JuegoForm onSubmit={handleCrear} loading={loading}  setDatosjuego={setDatosjuego} datosjuego={datosjuego}/>
        {error ?( <div >{String(error)}</div>):
        (<div >{success}</div>)}
      </div>
    </section>
  );
};

export default Admin;
