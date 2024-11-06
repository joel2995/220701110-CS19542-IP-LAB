
import java.io.IOException;
import java.io.PrintWriter;
import javax.servlet.ServletException;
import javax.servlet.annotation.WebServlet;
import javax.servlet.http.HttpServlet;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;
import java.sql.*;
@WebServlet("/StudentDetail")
public class StudentDetail extends HttpServlet {
	private static final long serialVersionUID = 1L;

	/**
	 * @see HttpServlet#doGet(HttpServletRequest request, HttpServletResponse response)
	 */
	protected void doGet(HttpServletRequest req, HttpServletResponse res) throws ServletException, IOException {
		String reg=req.getParameter("reg");
		res.setContentType("text/html");
		PrintWriter out = res.getWriter();
		try{  
			Class.forName("com.mysql.cj.jdbc.Driver");  
			Connection con=DriverManager.getConnection("jdbc:mysql://localhost:3306/library","root","");  
			PreparedStatement ps=con.prepareStatement("select * from students where reg like ?");  
			ps.setString(1,"%"+reg+"%");
			ResultSet rs=ps.executeQuery();  
			out.println("<!DOCTYPE html>\r\n"
					+ "<html>\r\n"
					+ "<head>\r\n"
					+ "   <title>Document</title>\r\n"
					+ "</head>\r\n"
					+ "<body>\r\n"
					+ "\r\n");
			if(rs.next()){  
				out.println("<div>reg no: "+rs.getString("reg")+"<br>Name: "+rs.getString("name")+"<br>City: "+rs.getString("city")+"</div><br>");  
			}  
			out.println("</body>\r\n"
			+ "</html>");
			out.println("<a href=\"index.html\">Back</a>");
			con.close();
		}
		catch(Exception e){
			System.out.println("problem");
			//e.printStackTrace();
		}
	}
	protected void doPost(HttpServletRequest req, HttpServletResponse res) throws ServletException, IOException {
		doGet(req,res);
	}
}
