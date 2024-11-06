<%@ page import="java.sql.*"%>  
  
<%  
	String s=request.getParameter("val");  
  	if(s==null)s="";
	String reg=s; 
	System.out.println(reg);
	try{  
		Class.forName("com.mysql.cj.jdbc.Driver");  
		Connection con=DriverManager.getConnection("jdbc:mysql://localhost:3306/library","root","");  
		PreparedStatement ps=con.prepareStatement("select * from students where reg like ?");  
		ps.setString(1,"%"+reg+"%");
		ResultSet rs=ps.executeQuery();  
		while(rs.next()){  
			out.println("<div onclick=\"location.href='StudentDetail?reg="+rs.getString("reg")+"'\" onmouseleave=erase(this) onmouseover=\"previewRequest(this)\" value=\""+rs.getString("reg")+"\" title=\""+rs.getString("name")+"\">"+rs.getInt("reg")+"<div></div></div><br>");  
		}  
		con.close();
	}
	catch(Exception e){
		System.out.println("problem");
		//e.printStackTrace();
	}  
%>  