/*import React, { useState, useEffect } from "react";

import Formulario from "../../components/FormularioComponent";

import axios from "axios";
import { Navigate } from "react-router-dom";
import verificar from "../../components/VerificarDatos";



const url = process.env.REACT_APP_BACK_URL;

const Register = () => {
        const [loading, setLoading] = useState(true);
        const [error, setError] = useState("");
        const [usuario, setUsuario] = useState([]);

        useEffect(()=>{
        setUsuario(localStorage.getItem("usuario"))
        if(verificar(usuario)){
        loadData()}
      },[]
      )
      const loadData= ()=>{
        axios
        .post(`${url}/login`,usuario)
        .then((response) => {
          setError(false);
          setLoading(false);
          localStorage.setItem("token",response.data.data.token)
          localStorage.setItem("usuariolog",response.data.usuario)
          Navigate('/')
        })
        .catch((error) => {
          setLoading(false);
          setError(error);
          
        });
      }

    return(
        <div>
            <Formulario/>
        </div>
    )
}
export default Register;*/
import React, { useState } from 'react';
import axios from 'axios';
import Formulario from '../../components/FormularioComponent';
import { Link } from 'react-router-dom';

const url = process.env.REACT_APP_BACK_URL;

const Register = () => {
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState('');
  const [success, setSuccess] = useState('');

  const handleRegister = ({ nombre_usuario, clave }) => {
    setLoading(true);
    setError('');
    setSuccess('');

    axios
      .post(`${url}/register`, { nombre_usuario, clave })
      .then((response) => {
        if (response.data.exists) {
          setError('El nombre de usuario ya está en uso.');
        } else {
          setSuccess('Registro exitoso. Puedes iniciar sesión.');
        }
      })
      .catch(() => {
        setError('Hubo un error en el registro. Inténtalo de nuevo.');
      })
      .finally(() => setLoading(false));
  };

  return (
    <section className='formularioPage'>
      <h2 className='tituloPage'>Registro</h2>
      <Formulario onData={handleRegister} loading={loading} />
      {error && <p style={{ color: 'red' }}>{error}</p>}
      {success && <p>{success} <Link to='/login'>Inicia Sesion</Link></p>}
      <p>¿Ya tienes cuenta? <Link to='/login'>Iniciar Sesion</Link></p>
      <button><Link to='/'>Ir a inicio </Link></button>      
    </section>
  );
};

export default Register;
