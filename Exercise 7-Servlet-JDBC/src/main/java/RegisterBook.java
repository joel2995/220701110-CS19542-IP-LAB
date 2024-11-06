

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
@WebServlet("/RegisterBook")
public class RegisterBook extends HttpServlet {
	private static final long serialVersionUID = 1L;

	/**
	 * @see HttpServlet#doGet(HttpServletRequest request, HttpServletResponse response)
	 */
	protected void doGet(HttpServletRequest request, HttpServletResponse response) throws ServletException, IOException {
		// TODO Auto-generated method stub
//		response.getWriter().append("Served at: ").append(request.getContextPath());
		response.setContentType("text/html");
		PrintWriter out = response.getWriter();
		try {
			Class.forName("com.mysql.cj.jdbc.Driver");
			String URL = "jdbc:mysql://localhost:3306/library";
			Connection conn = DriverManager.getConnection(URL, "root", "");
			PreparedStatement ps = conn.prepareStatement("insert into book (bookname,bookid,authorname,price) values (?,NULL,?,?)");
			String bookname=request.getParameter("bookname").toLowerCase();
			String authorname=request.getParameter("authorname").toLowerCase();
			String price = request.getParameter("price");
			ps.setString(1,bookname);
			ps.setString(2,authorname );
			ps.setString(3, price);
			int count = ps.executeUpdate();
			ps.close();
			bookname="%"+bookname+"%";
			ps=conn.prepareStatement("select * from Book where lower(bookname) like ?");
			ps.setString(1,bookname);
			ResultSet rs = ps.executeQuery();
			if(count==1) {
			out.println("<table border='1'>");
			out.println("<tr>");
			out.println("<td>Book Name</td>");
			out.println("<td>Book ID</td>");
			out.println("<td>Author Name</td>");
			out.println("<td>Price</td>");
			out.println("<td>Edit</td>");
			out.println("<td>Delete</td>");
			out.println("</tr>");
			if(rs.next()) {
				out.println("<tr>");
				out.println("<td>" + rs.getString("BookName") + "</td>");
				out.println("<td>" + rs.getInt("Bookid") + "</td>");
				out.println("<td>" + rs.getString("AuthorName") + "</td>");
				out.println("<td>" + rs.getInt("Price") + "</td>");
				out.println("<td><a href=\"EditBook?id="+rs.getInt("Bookid")+"\">Edit</a></td>");
				out.println("<td><a href=\"DeleteBook?id="+rs.getInt("Bookid")+"\">Delete</a></td>");
				out.println("</tr>");
			}
			else
				out.print("<tr><td>Data missmatched<?td></tr>");
			out.println("</table>");
			}
			else
				out.print("Error occured");
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
