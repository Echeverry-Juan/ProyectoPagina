import React, { useState } from 'react';
import axios from 'axios';
import Formulario from '../../components/FormularioComponent';
import { useNavigate, Link } from 'react-router-dom';

const url = process.env.REACT_APP_BACK_URL;

const Login = () => {
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState('');
  const navigate = useNavigate();

  const handleLogin = ({ nombre_usuario, clave }) => {
    setLoading(true);
    setError('');

    axios
      .post(`${url}/login`, { nombre_usuario, clave })
      .then((response) => {
        const token = response.data; 
        
        if (token) {
          const Time = new Date().getTime() + 60 * 60 * 1000; // 1 hora en milisegundos
          localStorage.setItem('token', token);
          localStorage.setItem('nombre_usuario', nombre_usuario);
          localStorage.setItem('token-time', Time);
          navigate('/');
        } else {
          setError('El nombre de usuario o la clave son incorrectos.');
        }
      })
      .catch((error) => {
        if (error.response && error.response.status === 401) {
          setError('El nombre de usuario o la clave son incorrectos.');
        } else if (error.response && error.response.status === 400) {
          setError('Debe ingresar el nombre de usuario y clave.');
        } else {
          setError('Hubo un error al iniciar sesión. Inténtalo de nuevo.');
        }
      })
      .finally(() => setLoading(false));
  };

  return (
    <section className='formularioPage'>
      <h2 className='tituloPage'>Iniciar Sesión</h2>
      <Formulario onData={handleLogin} loading={loading} />
      {error && <p >{error}</p>}
      <p>¿No tienes cuenta? <Link to='/register'>Regístrate</Link></p>
      <button><Link to='/'>Ir a inicio </Link></button>
    </section>
  );
};

export default Login;

