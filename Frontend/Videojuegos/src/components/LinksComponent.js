import Login from "./loginComponent";
import Registro from "./registroComponent";

export default function Links(){
    return(
        <section className="header-links">
            <Login/>
            <Registro/>
        </section>
        
    )    
}