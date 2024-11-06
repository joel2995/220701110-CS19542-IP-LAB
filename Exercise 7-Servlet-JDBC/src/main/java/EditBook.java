

import java.io.*;
import javax.servlet.ServletException;
import javax.servlet.annotation.WebServlet;
import javax.servlet.http.HttpServlet;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;
import java.sql.*;

/**
 * Servlet implementation class Book
 */
@WebServlet("/EditBook")
public class EditBook extends HttpServlet {
	private static final long serialVersionUID = 1L;

	/**
	 * @see HttpServlet#doGet(HttpServletRequest request, HttpServletResponse response)
	 */
	protected void doGet(HttpServletRequest request, HttpServletResponse response) throws ServletException, IOException {
		// TODO Auto-generated method stub
		response.setContentType("text/html");
		PrintWriter out = response.getWriter();
		try {
			Class.forName("com.mysql.cj.jdbc.Driver");
			String URL = "jdbc:mysql://localhost:3306/library";
			Connection conn = DriverManager.getConnection(URL, "root", "");
			PreparedStatement ps = conn.prepareStatement("select * from book where bookid=?");
			String id = request.getParameter("id");
			ps.setString(1,id);
			ResultSet rs = ps.executeQuery();
			out.println("<form action=\"UpdateBook\" method=\"post\"><table border='1'>");
			if(rs.next()) {
				out.println("<tr><td><label>Book Name:</label></td><td><input name=\"bookname\" value=\""+ rs.getString("BookName") +"\"> </td></tr>");
				out.println("<tr><td><label>Author Name:</label></td><td><input name=\"authorname\" value=\""+ rs.getString("authorName") +"\"> </td></tr>");
				out.println("<tr><td><label>Price:</label></td><td><input name=\"price\" value=\""+ rs.getString("price") +"\"> </td></tr>");
				out.println("<style>.id{display:none;}</style>");
				out.println("<input type=\"hidden\" name=\"bookid\" value=\""+ rs.getString("bookid") +"\" >");
			}
			else
				out.print("<tr><td>Data missmatched</td></tr>");
			
			out.println("<tr><td><input type=\"submit\" value=\"Update\"></td></tr></table></form>");
			out.println("<a href=\"home.html\">Back</a>");
			ps.close();
			conn.close();
		} catch (Exception e) {
			out.println(e);
		}
	}

	/**
	 * @see HttpServlet#doPost(HttpServletRequest request, HttpServletResponse response)
	 */
	protected void doPost(HttpServletRequest request, HttpServletResponse response) throws ServletException, IOException {
		// TODO Auto-generated method stub
		doGet(request, response);
	}

}
