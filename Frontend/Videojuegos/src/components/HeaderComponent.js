import './Estilos.css';
import Imagen from './ImagenComponent';

import Links from './LinksComponent';

//"https://i.pinimg.com/564x/13/08/fe/1308fedfdfcf82d3d4f85e98bd5f863f.jpg"

const Header=()=> {
    return (
      <header>
      <div className='contenedor-logo'>
      <p className='logo'>
        <Imagen
        />
       </p>
       <h1 className='tituloPage'>
        Videojuegos
       </h1>
       <Links/>
      </div>
      </header>
    )
  }

export default function HeaderComponent(){
    return (
        <section>
              <Header />
              
        </section>
    )
  }