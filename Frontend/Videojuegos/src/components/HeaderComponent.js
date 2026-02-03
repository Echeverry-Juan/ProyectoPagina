import './Estilos.css';
import Imagen from './ImagenComponent';
import Links from './LinksComponent';


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