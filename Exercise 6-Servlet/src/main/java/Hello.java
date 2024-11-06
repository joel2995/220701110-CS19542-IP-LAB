

import java.io.IOException;
import javax.servlet.ServletException;
import javax.servlet.annotation.WebServlet;
import javax.servlet.http.HttpServlet;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;
import java.io.*;

/**
 * Servlet implementation class Hello
 */
@WebServlet("/Hello")
public class Hello extends HttpServlet {
	private static final long serialVersionUID = 1L;

	/**
	 * @see HttpServlet#doGet(HttpServletRequest request, HttpServletResponse response)
	 */
	protected void doGet(HttpServletRequest req, HttpServletResponse res) throws ServletException, IOException {
		// TODO Auto-generated method stub
//		response.getWriter().append("Served at: ").append(request.getContextPath());
		res.setContentType("text/html");
		PrintWriter out = res.getWriter();
		
		out.print("<html><head><title>Details</title><link rel=\"stylesheet\" href=\"style1.css\"></head><body>");
//		out.print("<h3>Hello Servlet</h3>");
		String regno=req.getParameter("regno");
		String name=req.getParameter("name");
		int year =Integer.parseInt(req.getParameter("year"));
		String gender =req.getParameter("gender");
		int semester = Integer.parseInt(req.getParameter("semester"));
		String subcode=req.getParameter("sub code");
		String subname=req.getParameter("sub name");
		int credit=Integer.parseInt(req.getParameter("credit"));
		String coursetype=req.getParameter("course type");
		System.out.println(regno+" "+name+" "+year+" "+gender+" "+semester+" "+subcode+" "+subname+" "+credit+" "+coursetype);
		out.print("<div class=\"container\"><div><h1>Details</h1></div>");
		out.print("<div>Reg NO: "+regno+"</div>");
		out.print("<div>Name: "+name+"</div>");
		out.print("<div>Year: "+year+"</div>");
		out.print("<div>Gender: "+gender+"</div>");
		out.print("<div>Sub Code: "+subcode+"</div>");
		out.print("<div>Sub Name: "+subname+"</div>");
		out.print("<div>Credit: "+credit+"</div>");
		out.print("<div>Course Type: "+coursetype+"</div>");
		out.print("</div></body></html>");
		
		
	}
	@Override
	protected void doPost(HttpServletRequest req, HttpServletResponse res) throws ServletException, IOException {
		// TODO Auto-generated method stub
		doGet(req, res);
	}

}
